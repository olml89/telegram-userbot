<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Status\LogRecord;

use olml89\TelegramUserbot\Shared\Logger\LogRecord\InfoLogRecord;

final readonly class SubscribedToChannel extends InfoLogRecord
{
    public string $channel;

    public function __construct(string $channel)
    {
        parent::__construct(message: 'Subscribed to Redis channel');

        $this->channel = $channel;
    }

    protected function context(): array
    {
        return [
            'channel' => $this->channel,
        ];
    }
}
