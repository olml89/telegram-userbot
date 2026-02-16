<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Logger\LogRecord;

use Psr\Log\LoggerInterface;

abstract readonly class InfoLogRecord extends LogRecord implements Loggable
{
    public function log(LoggerInterface $logger): void
    {
        $logger->info(
            $this->message,
            array_filter(
                $this->context(),
                fn(mixed $value): bool => !is_null($value),
            ),
        );
    }
}
