<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use olml89\TelegramUserbot\Shared\Bot\Process\Process;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessManager;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStartedException;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\ErrorLogRecord;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;

/**
 * It recovers the API state and broadcasts the current status to the bot-manager, launching the request-status script
 * in a separate non-blocking process.
 */
final readonly class CleanupRunner
{
    public function __construct(
        private LoggableLogger $loggableLogger,
        private ProcessManager $processManager,
    ) {}

    public function run(): void
    {
        try {
            $this->processManager->start(Process::RequestStatus);
        } catch (ProcessNotStartedException $e) {
            $this->loggableLogger->log(new ErrorLogRecord('Error running cleanup', $e));
        }
    }
}
