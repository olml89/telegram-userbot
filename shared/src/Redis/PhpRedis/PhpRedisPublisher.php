<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Redis\PhpRedis;

use olml89\TelegramUserbot\Shared\Redis\RedisConfig;
use olml89\TelegramUserbot\Shared\Redis\RedisPublisher;
use Redis;

final readonly class PhpRedisPublisher implements RedisPublisher
{
    private Redis $redis;

    public function __construct(RedisConfig $config)
    {
        $this->redis = new Redis();
        $this->redis->pconnect(host: $config->host, port: $config->port);
    }

    public function publish(string $channel, string $message): void
    {
        $this->redis->publish($channel, $message);
    }
}
