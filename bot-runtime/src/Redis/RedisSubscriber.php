<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotRuntime\Redis;

interface RedisSubscriber
{
    /**
     * @param callable(string): void $callback
     */
    public function subscribe(string $channel, callable $callback): void;
}
