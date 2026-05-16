<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotRuntime\Logger\LogRecord;

interface LoggableLogger
{
    public function log(Loggable $loggable): void;
}
