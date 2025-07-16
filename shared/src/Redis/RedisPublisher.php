<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Redis;

interface RedisPublisher
{
    public function publish(string $channel, string $message): void;
}
