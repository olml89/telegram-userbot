<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Redis;

interface RedisSubscriber
{
    /**
     * @param callable(string): void $callback
     */
    public function subscribe(string $channel, callable $callback): void;
}
