<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\ExceptionEvent;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;
use Twig\Environment;

final readonly class ReportableExceptionEventHandler implements ExceptionEventHandler
{
    public function __construct(
        private ExceptionHandler $exceptionHandler,
        private Environment $twig,
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
        $this->exceptionHandler->handle($exception);
        $response = $this->createUiResponse($exception);

        $event->setResponse($response);
    }

    private function createUiResponse(Throwable $throwable): Response
    {
        $statusCode = $throwable instanceof HttpExceptionInterface
            ? $throwable->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        $errorTemplate = match($statusCode) {
            Response::HTTP_NOT_FOUND => 'error/404.html.twig',
            default => 'error/500.html.twig',
        };

        try {
            return new Response(
                content: $this->twig->render($errorTemplate, [
                    'active_menu' => null,
                ]),
                status: $statusCode,
            );
        } catch (Throwable) {
            return new Response(content: 'There was an unknown error', status: $statusCode);
        }
    }
}
