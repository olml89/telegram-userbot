<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\MadelineProto;

use Amp;
use Closure;
use danog\MadelineProto\API;
use olml89\TelegramUserbot\Bot\Bot\Status\StatusBroadcaster;
use olml89\TelegramUserbot\Bot\Output\ExceptionOutput;
use olml89\TelegramUserbot\Bot\Output\MadelineProtoFileLoggerOutput;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\ErrorLogRecord;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use Throwable;

/**
 * Workaround to solve the problem created by MadelineProto by making its system so opinionated and non-extensible.
 *
 * Basically, there's no clean way of getting the output from the inside of the MadelineProto API IPC synchronous
 * workers: phoneLogin, completePhoneLogin and logout. When instantiating an API object from scratch, the Logger is
 * always overridden by the default one. However, even if you could inject a custom one, the type of the Logger is
 * always overridden to be FILE_LOGGER if MadelineProto detects it is in an IPC environment.
 *
 * So, the only solution is to launch the IPC starting method in asynchronous mode using amphp and then capture
 * the output of the log file in parallel.
 */
final readonly class IpcWorkerOutputCatcher
{
    public function __construct(
        private string $logPath,
        private ApiWrapper $apiWrapper,
        private StatusBroadcaster $statusBroadcaster,
        private LoggableLogger $loggableLogger,
        private Closure $ipcWorkerProcess,
    ) {
    }

    public function run(mixed ...$arguments): void
    {
        $future = Amp\async(function () use ($arguments): void {
            $ipcProcessFuture = $this->ipcProcessFuture(...$arguments);

            $logWatcherProcess = Amp\Process\Process::start([
                'tail',
                '-F',
                $this->logPath,
            ]);

            $logWatcherProcessCancellation = new Amp\DeferredCancellation();
            $logWatcherFuture = $this->logWatcherFuture($logWatcherProcess, $logWatcherProcessCancellation);

            try {
                $ipcProcessFuture->await();
            } finally {
                $logWatcherProcessCancellation->cancel();
                $this->terminateProcess($logWatcherProcess);
                $logWatcherProcess->join();
            }

            $logWatcherFuture->await();
        });

        $future->await();
        $this->statusBroadcaster->broadcast($this->apiWrapper);
    }

    /**
     * @return Amp\Future<void>
     */
    private function ipcProcessFuture(mixed ...$arguments): Amp\Future
    {
        /** @var Amp\Future<void> */
        return Amp\async(function () use ($arguments): void {
            try {
                ($this->ipcWorkerProcess)(...$arguments);
            } catch (Throwable $e) {
                $this->loggableLogger->log(new ErrorLogRecord('Error during IPC process', $e));
                $this->statusBroadcaster->broadcast($this->apiWrapper, new ExceptionOutput($e));
            }
        });
    }

    /**
     * @return Amp\Future<void>
     */
    private function logWatcherFuture(Amp\Process\Process $process, Amp\DeferredCancellation $cancellation): Amp\Future
    {
        /** @var Amp\Future<void> */
        return Amp\async(function () use ($process, $cancellation): void {
            try {
                $logReadStream = $process->getStdout();

                while (true) {
                    if ($cancellation->getCancellation()->isRequested()) {
                        break;
                    }

                    $chunk = $logReadStream->read();

                    if (is_null($chunk)) {
                        break;
                    }

                    foreach (explode(PHP_EOL, $chunk) as $line) {
                        $output = new MadelineProtoFileLoggerOutput($line);
                        $this->statusBroadcaster->broadcast($this->apiWrapper, $output);
                    }
                }
            } catch (Throwable $e) {
                $this->loggableLogger->log(new ErrorLogRecord('Error during log watcher process', $e));
                $this->statusBroadcaster->broadcast($this->apiWrapper, new ExceptionOutput($e));
            }
        });
    }

    private function terminateProcess(Amp\Process\Process $process): void
    {
        posix_kill($process->getPid(), signal: SIGTERM);
        Amp\delay(timeout: 0.2);

        exec(sprintf('pgrep -P %s', $process->getPid()), output: $childrenPids);

        $childrenPids = array_filter(
            array_map(
                fn (string $pid): int => intval($pid),
                $childrenPids,
            ),
            fn (int $pid): bool => $pid > 0,
        );

        foreach ($childrenPids as $pid) {
            posix_kill($pid, signal: SIGTERM);
            Amp\delay(timeout: 0.1);

            if (posix_kill($pid, signal: SIG_DFL)) {
                posix_kill($pid, signal: SIGKILL);
            }
        }

        if (posix_kill($process->getPid(), signal: SIG_DFL)) {
            posix_kill($process->getPid(), signal: SIGKILL);
        }
    }
}
