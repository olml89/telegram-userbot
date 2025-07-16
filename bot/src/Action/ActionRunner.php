<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use olml89\TelegramUserbot\Bot\Action\LogRecord\ActionFinished;
use olml89\TelegramUserbot\Bot\Action\LogRecord\ActionStarted;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\ErrorLogRecord;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use Throwable;

/**
 * Wrapper to run Actions in a safe context. Log exceptions to avoid breaking down the bot process.
 */
final readonly class ActionRunner
{
    public function __construct(
        private Action $action,
        private LoggableLogger $loggableLogger,
    ) {
    }

    public function run(): void
    {
        try {
            $this->loggableLogger->log(new ActionStarted($this->action));
            $this->action->run();
            $this->loggableLogger->log(new ActionFinished($this->action));
        } catch (Throwable $e) {
            $this->loggableLogger->log(new ErrorLogRecord('Error running action', $e));
        }
    }
}
