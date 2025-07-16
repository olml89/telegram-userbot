<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Logger\LogRecord;

use Psr\Log\LoggerInterface;

interface Loggable
{
    public function log(LoggerInterface $logger): void;
}
