<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Redis;

use Exception;

final class RedisStorageException extends Exception
{
    public static function get(string $key): self
    {
        return new self(sprintf('Could not get key: %s', $key));
    }

    public static function set(string $key): self
    {
        return new self(sprintf('Could not set key: %s', $key));
    }

    public static function del(string $key): self
    {
        return new self(sprintf('Could not delete key: %s', $key));
    }
}
