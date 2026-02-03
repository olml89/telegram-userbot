<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

final readonly class HttpExceptionMapper
{
    public function map(Throwable $exception): HttpExceptionInterface
    {
        return match (true) {
            $exception instanceof HttpExceptionInterface => $exception,
            $exception instanceof NotFoundException => new NotFoundHttpException(
                message: $exception->getMessage(),
                previous: $exception,
            ),
            default => new HttpException(
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: 'Unexpected error',
                previous: $exception,
            ),
        };
    }
}
