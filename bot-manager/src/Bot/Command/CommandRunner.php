<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use olml89\TelegramUserbot\BotManager\Bot\Command\LogRecord\HandlingCommand;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\ErrorLogRecord;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use Throwable;

/**
 * Wrapper to run Commands in a safe context. Log exceptions here on the command channel to avoid logging them on the
 * webserver-socket channel, then rethrow them again to be broadcasted to the websocket clients.
 */
final readonly class CommandRunner
{
    public function __construct(
        private CommandHandler $commandHandler,
        private LoggableLogger $loggableLogger,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function run(Command $command): void
    {
        try {
            $this->loggableLogger->log(new HandlingCommand($command));
            $command->handle($this->commandHandler);
        } catch (Throwable $e) {
            $this->loggableLogger->log(new ErrorLogRecord('Error running the command', $e));

            /**
             * Rethrow the exception, so it can be caught by the WebSocketServer and broadcast to the websocket clients.
             */
            throw $e;
        }
    }
}
