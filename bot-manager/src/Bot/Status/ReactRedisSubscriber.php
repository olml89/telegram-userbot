<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Status;

use Clue\React\Redis\RedisClient;
use olml89\TelegramUserbot\Shared\Redis\RedisConfig;
use olml89\TelegramUserbot\Shared\Redis\RedisSubscriber;

final readonly class ReactRedisSubscriber implements RedisSubscriber
{
    private RedisClient $redisClient;

    public function __construct(RedisConfig $config)
    {
        $this->redisClient = new RedisClient(sprintf('%s:%s', $config->host, $config->port));
    }

    /**
     * @param callable(string): void $callback
     */
    public function subscribe(string $channel, callable $callback): void
    {
        $this->redisClient->callAsync('subscribe', $channel);

        $this->redisClient->on('message', function (string $channel, string $message) use ($callback): void {
            $callback($message);
        });
    }
}
