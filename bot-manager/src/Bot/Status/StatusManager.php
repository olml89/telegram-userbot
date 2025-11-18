<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Status;

use olml89\TelegramUserbot\BotManager\Bot\Status\LogRecord\ReceivedStatus;
use olml89\TelegramUserbot\BotManager\Websocket\WebSocketConnectionPool;
use olml89\TelegramUserbot\Shared\App\AppConfig;
use olml89\TelegramUserbot\Shared\App\Environment\Environment;
use olml89\TelegramUserbot\Shared\Bot\Status\Status;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use Throwable;

/**
 * It has control over the current MadelineProto API status on the bot container via Redis subscription to Status
 * update events. It broadcasts the new Status to the websocket connections on status update.
 */
final readonly class StatusManager
{
    public function __construct(
        private AppConfig $appConfig,
        private StatusSubscriber $statusSubscriber,
        private StatusVault $statusVault,
        private WebSocketConnectionPool $socketConnectionPool,
        private LoggableLogger $loggableLogger,
    ) {
        $this->statusSubscriber->subscribe($this);
    }

    public function status(): Status
    {
        return $this->statusVault->get();
    }

    /**
     * Logs, stores and emits a received status
     */
    public function process(Status $status): void
    {
        $this->loggableLogger->log(new ReceivedStatus($status));
        $this->statusVault->set($status);

        $this->emit();
    }

    /**
     * Emits current status with or without its message replaced by an error message
     */
    public function emit(?Throwable $e = null): void
    {
        $status = $this->statusVault->get();

        if (!is_null($e)) {
            $status = $status->withMessage(
                $this->appConfig->environment === Environment::Development ? $e : $e->getMessage()
            );
        }

        $this->socketConnectionPool->emit($status);
    }
}
