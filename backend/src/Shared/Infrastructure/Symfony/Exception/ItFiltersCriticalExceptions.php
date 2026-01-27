<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

trait ItFiltersCriticalExceptions
{
    private function isCritical(Throwable $exception): bool
    {
        if (!$exception instanceof HttpExceptionInterface) {
            return true;
        }

        return $exception->getStatusCode() >= Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
