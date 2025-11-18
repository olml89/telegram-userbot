<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Process;

use olml89\TelegramUserbot\Shared\Bot\Process\LogRecord\StartedProcess;
use olml89\TelegramUserbot\Shared\Bot\Process\LogRecord\StoppedProcess;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;

final readonly class Process
{
    private ProcessType $type;

    public function __construct(ProcessType $processType)
    {
        $this->type = $processType;
    }

    /**
     * @throws ProcessNotStartedException
     */
    public function start(ProcessManager $processManager, LoggableLogger $loggableLogger): ProcessResult
    {
        $loggableLogger->log(new StartedProcess($this->type));

        return $processManager->start($this->type);
    }

    /**
     * @throws ProcessNotStoppedException
     */
    public function stop(ProcessManager $processManager, LoggableLogger $loggableLogger): ProcessResult
    {
        $loggableLogger->log(new StoppedProcess($this->type));

        return $processManager->stop($this->type);
    }

    public function isRunning(ProcessManager $processManager): bool
    {
        return $processManager->isRunning($this->type);
    }
}
