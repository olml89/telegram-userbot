<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Supervisor;

use olml89\TelegramUserbot\Shared\App\ExecResult;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessManager;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStartedException;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStoppedException;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessResult;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessType;

/**
 * It executes supervisorctl with a given config and returns the result to be examined
 */
final readonly class SupervisorCtl implements ProcessManager
{
    public function __construct(
        private SupervisorConfig $supervisorConfig,
    ) {
    }

    private function execute(SupervisorCommand $supervisorCommand, ProcessType $processType): ExecResult
    {
        $return = exec(
            command: sprintf(
                'supervisorctl -c %s %s %s',
                $this->supervisorConfig->configPath,
                $supervisorCommand->value,
                $processType->value,
            ),
            output: $output,
            result_code: $code,
        );

        return new ExecResult($return, $output, $code);
    }

    /**
     * @throws ProcessNotStartedException
     */
    public function start(ProcessType $processType): ProcessResult
    {
        $executed = $this->execute(SupervisorCommand::Start, $processType);
        $processResult = ProcessResult::Started;

        if (!$executed->hasProcessResult($processResult)) {
            throw new ProcessNotStartedException($processType, $executed);
        }

        return $processResult;
    }

    /**
     * @throws ProcessNotStoppedException
     */
    public function stop(ProcessType $processType): ProcessResult
    {
        $executed = $this->execute(SupervisorCommand::Stop, $processType);
        $processResult = ProcessResult::Stopped;

        if (!$executed->hasProcessResult($processResult)) {
            throw new ProcessNotStoppedException($processType, $executed);
        }

        return $processResult;
    }

    public function isRunning(ProcessType $processType): bool
    {
        return $this
            ->execute(SupervisorCommand::Status, $processType)
            ->hasProcessResult(ProcessResult::Running);
    }
}
