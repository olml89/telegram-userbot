<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Bot\Status;

use danog\MadelineProto\API;
use olml89\TelegramUserbot\Bot\Output\Output;
use olml89\TelegramUserbot\Shared\Bot\Status\LogRecord\EmittedStatus;
use olml89\TelegramUserbot\Shared\Bot\Status\Status;
use olml89\TelegramUserbot\Shared\Bot\Status\StatusEmitter;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;

/**
 * It gets the current Status and broadcasts it to a StatusSubscriber in bot-manager (in the practice, a
 * RedisSubscriber).
 *
 * This way we achieve a real-time pub/sub communication between bot and bot-manager.
 */
final readonly class StatusBroadcaster implements StatusEmitter
{
    public function __construct(
        private ApiStatusCalculator $apiStatusCalculator,
        private StatusPublisher $statusPublisher,
        private LoggableLogger $loggableLogger,
    ) {
    }

    public function emit(Status $status): void
    {
        $this->statusPublisher->publish($status);
        $this->loggableLogger->log(new EmittedStatus($status));
    }

    public function broadcast(?API $api = null, ?Output $output = null): void
    {
        if ($output instanceof Output && ! $output->isBroadcastable()) {
            return;
        }

        $status = $this
            ->apiStatusCalculator
            ->calculate($api)
            ->withMessage($output);

        $this->emit($status);
    }
}
