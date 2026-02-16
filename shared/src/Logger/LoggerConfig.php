<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Logger;

use Monolog\Level;

final readonly class LoggerConfig
{
    public function __construct(
        public string $logDirectory,
        public Level $level,
    ) {}
}
