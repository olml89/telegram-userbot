<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotRuntime\Redis;

interface RedisPublisher
{
    public function publish(string $channel, string $message): void;
}
