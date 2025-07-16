<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Websocket\LogRecord;

use olml89\TelegramUserbot\BotManager\Websocket\WebSocketConnectionWrapper;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\InfoLogRecord;

final readonly class OpenedConnection extends InfoLogRecord
{
    private WebSocketConnectionWrapper $connection;

    public function __construct(WebSocketConnectionWrapper $connection)
    {
        parent::__construct(message: 'Opened connection');

        $this->connection = $connection;
    }

    /**
     * @return array<string, mixed>
     */
    protected function context(): array
    {
        return [
            'ip' => $this->connection->remoteAddress(),
            'resourceId' => $this->connection->resourceId(),
        ];
    }
}
