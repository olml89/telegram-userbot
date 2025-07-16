<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Logger\LogRecord;

interface LoggableLogger
{
    public function log(Loggable $loggable): void;
}
