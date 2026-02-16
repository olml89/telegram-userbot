<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Handler\ExceptionHandler;
use Psr\Log\LoggerInterface;
use Throwable;

final readonly class ExceptionLogger implements ExceptionHandler
{
    use ItFiltersCriticalExceptions;

    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function handle(Throwable $exception, bool $handled = true): void
    {
        $errorMessage = sprintf(
            '%s PHP Exception %s: "%s" at %s line %s',
            $handled ? 'Caught' : 'Uncaught',
            $exception::class,
            $exception->getMessage(),
            basename($exception->getFile()),
            $exception->getLine(),
        );

        $context = [
            'exception' => $exception,
        ];

        $this->isCritical($exception)
            ? $this->logger->critical($errorMessage, $context)
            : $this->logger->error($errorMessage, $context);
    }
}
