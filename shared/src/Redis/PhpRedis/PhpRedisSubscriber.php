<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Redis\PhpRedis;

use olml89\TelegramUserbot\Shared\Redis\RedisConfig;
use olml89\TelegramUserbot\Shared\Redis\RedisSubscriber;
use Redis;

final readonly class PhpRedisSubscriber implements RedisSubscriber
{
    private Redis $redis;

    public function __construct(RedisConfig $config)
    {
        $this->redis = new Redis();
        $this->redis->pconnect(host: $config->host, port: $config->port);
        $this->redis->setOption(Redis::OPT_READ_TIMEOUT, -1);
    }

    /**
     * @param callable(string): void $callback
     */
    public function subscribe(string $channel, callable $callback): void
    {
        $this->redis->subscribe(
            channels: [$channel],
            cb: function (Redis $redis, string $channel, string $message) use ($callback): void {
                $callback($message);
            },
        );
    }
}
