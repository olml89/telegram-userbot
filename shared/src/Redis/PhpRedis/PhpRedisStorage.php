<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Redis\PhpRedis;

use olml89\TelegramUserbot\Shared\Redis\RedisConfig;
use olml89\TelegramUserbot\Shared\Redis\RedisStorage;
use olml89\TelegramUserbot\Shared\Redis\RedisStorageException;
use Redis;
use Stringable;

final readonly class PhpRedisStorage implements RedisStorage
{
    private Redis $redis;

    public function __construct(RedisConfig $config)
    {
        $this->redis = new Redis();
        $this->redis->pconnect(host: $config->host, port: $config->port);
    }

    public function get(string $key): ?string
    {
        $value = $this->redis->get($key);

        return is_string($value) ? $value : null;
    }

    /**
     * @throws RedisStorageException
     */
    public function set(string $key, string|Stringable $value): void
    {
        $value = is_string($value) ? $value : (string) $value;
        $result = $this->redis->set($key, $value);

        if ($result !== true) {
            throw RedisStorageException::set($key);
        }
    }

    /**
     * @throws RedisStorageException
     */
    public function del(string $key): void
    {
        $result = $this->redis->del($key);

        if (!is_int($result) || $result < 1) {
            throw RedisStorageException::del($key);
        }
    }
}
