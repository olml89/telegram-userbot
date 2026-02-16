<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Error;

final readonly class SentryConfig
{
    public function __construct(
        public string $dsn,
    ) {}
}
