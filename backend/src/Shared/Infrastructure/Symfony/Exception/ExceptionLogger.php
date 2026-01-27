<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\ExceptionHandler;
use Psr\Log\LoggerInterface;
use Throwable;

abstract readonly class ExceptionLogger implements ExceptionHandler
{
    use ItFiltersCriticalExceptions;

    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    abstract protected function exceptionName(Throwable $exception): string;

    public function handle(Throwable $exception): void
    {
        $errorMessage = sprintf(
            '%s: "%s" at %s line %s',
            $this->exceptionName($exception),
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
