<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Logger;

use olml89\TelegramUserbot\Shared\Logger\LogRecord\Loggable;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use Psr\Log\LoggerInterface;

final readonly class LogRecordLogger implements LoggableLogger
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function log(Loggable $loggable): void
    {
        $loggable->log($this->logger);
    }
}
