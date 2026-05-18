<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotRuntime\App;

use olml89\TelegramUserbot\Application\Environment\Environment;

final readonly class AppConfig
{
    public function __construct(
        public Environment $environment,
    ) {}
}
