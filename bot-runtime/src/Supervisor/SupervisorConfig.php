<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotRuntime\Supervisor;

final readonly class SupervisorConfig
{
    public function __construct(
        public string $configPath,
    ) {}
}
