<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\ExceptionHandler;
use Throwable;

final readonly class ExceptionRethrower implements ExceptionHandler
{
    /**
     * It re-throws an exception.
     * This is used on development to escape the nice visualization of catched exceptions.
     *
     * @throws Throwable
     */
    public function handle(Throwable $exception): void
    {
        throw $exception;
    }
}
