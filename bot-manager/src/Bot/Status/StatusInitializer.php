<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Status;

use olml89\TelegramUserbot\BotManager\Bot\Command\Command\RequestStatusCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandHandler;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\ErrorLogRecord;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use Throwable;

/**
 * Wrapper to run RequestStatusCommand in a safe context at the beginning of the application lifecycle. This way we get
 * the initial status of the bot.
 *
 * Log exceptions to avoid breaking down the websocket server.
 */
final readonly class StatusInitializer
{
    public function __construct(
        private CommandHandler $commandHandler,
        private LoggableLogger $loggableLogger,
    ) {
    }

    public function initialize(): void
    {
        try {
            new RequestStatusCommand()->handle($this->commandHandler);
        } catch (Throwable $e) {
            $this->loggableLogger->log(new ErrorLogRecord('Error initializing status', $e));
        }
    }
}
