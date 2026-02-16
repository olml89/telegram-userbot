<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Redis;

final readonly class RedisConfig
{
    public function __construct(
        public string $host,
        public string $statusChannel,
        public string $phoneCodeStorageKey,
        public int $port = 6379,
    ) {}
}
