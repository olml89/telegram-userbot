<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Exception;

use Psr\Log\LoggerInterface;
use Sentry\State\HubInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

final readonly class ProductionExceptionEventHandler implements ExceptionEventHandler
{
    public function __construct(
        private LoggerInterface $logger,
        private HubInterface $sentry,
    ) {
    }

    public function handle(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        /**
         * Symfony only logs exceptions automatically if, when 'kernel.exception' handling is finished, no Response
         * is associated to the event.
         *
         * Since here we are attaching a response, the exception won't be logged, so we have to do it manually.
         */
        $this->logException($exception);

        if ($this->isCritical($exception)) {
            $this->sentry->captureException($exception);
        }

        $event->setResponse($this->createUiResponse());
    }

    private function logException(Throwable $exception): void
    {
        $errorMessage =  sprintf(
            'Uncaught PHP Exception %s: "%s" at %s line %s',
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

    private function isCritical(Throwable $exception): bool
    {
        return !$exception instanceof HttpExceptionInterface
            || $exception->getStatusCode() >= Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    private function createUiResponse(): Response
    {
        return new Response('There was an error.');
    }
}
