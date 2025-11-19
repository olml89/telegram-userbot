<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Redis;

use Amp;
use olml89\TelegramUserbot\Shared\Redis\RedisConfig;
use olml89\TelegramUserbot\Shared\Redis\RedisPublisher;

final readonly class AmphpRedisPublisher implements RedisPublisher
{
    private Amp\Redis\RedisClient $redis;

    public function __construct(RedisConfig $redisConfig)
    {
        $url = sprintf('redis://%s:%s', $redisConfig->host, $redisConfig->port);
        $this->redis = Amp\Redis\createRedisClient($url);
    }

    public function publish(string $channel, string $message): void
    {
        Amp\async(function () use ($channel, $message): void {
            $this->redis->publish($channel, $message);
        });
    }
}
