<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Websocket\LogRecord;

use olml89\TelegramUserbot\BotManager\Websocket\WebSocketServerConfig;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\InfoLogRecord;

final readonly class Listening extends InfoLogRecord
{
    public WebSocketServerConfig $config;

    public function __construct(WebSocketServerConfig $config)
    {
        parent::__construct('Listening websocket connections');

        $this->config = $config;
    }

    protected function context(): array
    {
        return [
            'host' => $this->config->host,
            'port' => $this->config->port,
        ];
    }
}
