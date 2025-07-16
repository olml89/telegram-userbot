<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Redis;

use Stringable;

interface RedisStorage
{
    public function get(string $key): ?string;

    /** @throws RedisStorageException */
    public function set(string $key, string|Stringable $value): void;

    /** @throws RedisStorageException */
    public function del(string $key): void;
}
