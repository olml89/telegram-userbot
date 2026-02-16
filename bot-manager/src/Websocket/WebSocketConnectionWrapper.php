<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Websocket;

use olml89\TelegramUserbot\Shared\Bot\Status\Status;
use Ratchet\ConnectionInterface;

final readonly class WebSocketConnectionWrapper
{
    public function __construct(
        private ConnectionInterface $connection,
    ) {}

    public function resourceId(): ?int
    {
        return property_exists($this->connection, 'resourceId') && is_int($this->connection->resourceId)
            ? $this->connection->resourceId
            : null;
    }

    public function remoteAddress(): ?string
    {
        return property_exists($this->connection, 'remoteAddress') && is_string($this->connection->remoteAddress)
            ? $this->connection->remoteAddress
            : null;
    }

    public function send(Status $status): void
    {
        $this->connection->send((string) $status);
    }
}
