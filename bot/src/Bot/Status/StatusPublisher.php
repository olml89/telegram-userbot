<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Bot\Status;

use olml89\TelegramUserbot\Shared\Bot\Status\Status;
use olml89\TelegramUserbot\Shared\Redis\RedisConfig;
use olml89\TelegramUserbot\Shared\Redis\RedisPublisher;

/**
 * It publishes Status updates to Redis, to be handled by a StatusHandler
 */
final readonly class StatusPublisher
{
    public function __construct(
        private RedisConfig $config,
        private RedisPublisher $publisher,
    ) {
    }

    public function publish(Status $status): void
    {
        $this->publisher->publish($this->config->statusChannel, (string) $status);
    }
}
