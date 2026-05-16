<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotRuntime\Logger\LogRecord;

use Psr\Log\LoggerInterface;

abstract readonly class InfoLogRecord extends LogRecord implements Loggable
{
    public function log(LoggerInterface $logger): void
    {
        $logger->info(
            message: $this->message,
            context: array_filter(
                $this->context(),
                fn(mixed $value): bool => !is_null($value),
            ),
        );
    }
}
