<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Bot;

final readonly class BotConfig
{
    public function __construct(
        public int $apiId,
        public string $apiHash,
        public string $phoneNumber,
        public string $username,
    ) {
    }
}
