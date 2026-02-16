<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Bot;

final readonly class BotLogFile
{
    public function __construct(
        private string $path,
    ) {}

    public function path(): string
    {
        return $this->path;
    }
}
