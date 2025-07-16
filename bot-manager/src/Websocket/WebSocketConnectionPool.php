<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Websocket;

use olml89\TelegramUserbot\Shared\Bot\Status\LogRecord\EmittedStatus;
use olml89\TelegramUserbot\Shared\Bot\Status\Status;
use olml89\TelegramUserbot\Shared\Bot\Status\StatusEmitter;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use Ratchet\ConnectionInterface;
use WeakMap;

/**
 * Manages websocket connections and broadcasts the Status to them on demand
 */
final class WebSocketConnectionPool implements StatusEmitter
{
    private readonly LoggableLogger $loggableLogger;

    /**
     * @var WeakMap<ConnectionInterface, WebSocketConnectionWrapper>
     */
    private WeakMap $connections;

    public function __construct(LoggableLogger $loggableLogger)
    {
        $this->loggableLogger = $loggableLogger;
        $this->connections = new WeakMap();
    }

    public function add(ConnectionInterface $connection): WebSocketConnectionWrapper
    {
        $this->connections[$connection] = new WebSocketConnectionWrapper($connection);

        return $this->connections[$connection];
    }

    public function remove(ConnectionInterface $connection): WebSocketConnectionWrapper
    {
        $webSocketConnection = $this->connections[$connection];
        unset($this->connections[$connection]);

        return $webSocketConnection;
    }

    public function emit(Status $status): void
    {
        foreach ($this->connections as $webSocketConnection) {
            $webSocketConnection->send($status);
        }

        $this->loggableLogger->log(new EmittedStatus($status));
    }
}
