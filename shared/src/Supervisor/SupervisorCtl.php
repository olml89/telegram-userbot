<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Supervisor;

use olml89\TelegramUserbot\Shared\App\ExecResult;
use olml89\TelegramUserbot\Shared\Bot\Process\Process;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessManager;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStartedException;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStoppedException;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessResult;

/**
 * It executes supervisorctl with a given config and returns the result to be examined
 */
final readonly class SupervisorCtl implements ProcessManager
{
    public function __construct(
        private SupervisorConfig $supervisorConfig,
    ) {
    }

    private function execute(string $supervisorCommand, Process $process): ExecResult
    {
        $return = exec(
            command: sprintf(
                'supervisorctl -c %s %s %s',
                $this->supervisorConfig->supervisorConfigPath,
                $supervisorCommand,
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
        $executed = $this->execute('start', $process);
        $processResult = ProcessResult::Started;

        if (!$executed->hasProcessResult($processResult)) {
            throw new ProcessNotStartedException($process, $executed);
        }

        return $processResult;
    }

    /**
     * @throws ProcessNotStoppedException
     */
    public function stop(Process $process): ProcessResult
    {
        $executed = $this->execute('stop', $process);
        $processResult = ProcessResult::Stopped;

        if (!$executed->hasProcessResult($processResult)) {
            throw new ProcessNotStoppedException($process, $executed);
        }

        return $processResult;
    }

    public function isRunning(Process $process): bool
    {
        return $this
            ->execute('status', $process)
            ->hasProcessResult(ProcessResult::Running);
    }
}
