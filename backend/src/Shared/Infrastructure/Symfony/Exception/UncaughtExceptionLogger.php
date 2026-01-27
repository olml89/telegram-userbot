<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception;

use Throwable;

final readonly class UncaughtExceptionLogger extends ExceptionLogger
{
    protected function exceptionName(Throwable $exception): string
    {
        return sprintf('Uncaught PHP Exception %s', $exception::class);
    }
}
