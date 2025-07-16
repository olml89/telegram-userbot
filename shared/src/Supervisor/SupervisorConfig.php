<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Supervisor;

final readonly class SupervisorConfig
{
    public function __construct(
        public string $supervisorConfigPath,
    ) {
    }
}
