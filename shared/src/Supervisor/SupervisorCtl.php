<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Supervisor;

use olml89\TelegramUserbot\Shared\App\ExecResult;
use olml89\TelegramUserbot\Shared\Bot\Process\LogRecord\StartedProcess;
use olml89\TelegramUserbot\Shared\Bot\Process\LogRecord\StoppedProcess;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessManager;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStartedException;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStoppedException;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessResult;
use olml89\TelegramUserbot\Shared\Bot\Process\Process;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;

/**
 * It executes supervisorctl with a given config and returns the result to be examined
 */
final readonly class SupervisorCtl implements ProcessManager
{
    public function __construct(
        private SupervisorConfig $supervisorConfig,
        private LoggableLogger $loggableLogger,
    ) {
    }

    private function execute(SupervisorCommand $supervisorCommand, Process $process): ExecResult
    {
        $return = exec(
            command: sprintf(
                'supervisorctl -c %s %s %s',
                $this->supervisorConfig->configPath,
                $supervisorCommand->value,
                $process->value,
            ),
            output: $output,
            result_code: $code,
        );

        return new ExecResult($return, $output, $code);
    }

    /**
     * @throws ProcessNotStartedException
     */
    public function start(Process $process): ProcessResult
    {
        $executed = $this->execute(SupervisorCommand::Start, $process);
        $processResult = ProcessResult::Started;

        if (!$executed->hasProcessResult($processResult)) {
            throw new ProcessNotStartedException($process, $executed);
        }

        $this->loggableLogger->log(new StartedProcess($process));

        return $processResult;
    }

    /**
     * @throws ProcessNotStoppedException
     */
    public function stop(Process $process): ProcessResult
    {
        $executed = $this->execute(SupervisorCommand::Stop, $process);
        $processResult = ProcessResult::Stopped;

        if (!$executed->hasProcessResult($processResult)) {
            throw new ProcessNotStoppedException($process, $executed);
        }

        $this->loggableLogger->log(new StoppedProcess($process));

        return $processResult;
    }

    public function isRunning(Process $process): bool
    {
        return $this
            ->execute(SupervisorCommand::Status, $process)
            ->hasProcessResult(ProcessResult::Running);
    }
}
