<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Websocket;

final readonly class WebSocketServerConfig
{
    public function __construct(
        public string $host,
        public int $port,
    ) {}

    public function uri(): string
    {
        return sprintf('%s:%s', $this->host, $this->port);
    }
}
