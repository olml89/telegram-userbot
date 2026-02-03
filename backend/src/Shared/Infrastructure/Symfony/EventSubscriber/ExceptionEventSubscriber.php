<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\EventSubscriber;

use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception\HttpExceptionMapper;
use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception\ExceptionSentryReporter;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

final readonly class ExceptionEventSubscriber
{
    public function __construct(
        private ExceptionSentryReporter $exceptionSentryReporter,
        private HttpExceptionMapper $httpExceptionMapper,
    ) {
    }

    /**
     * Priority: -10
     * To fire after ErrorListener::logKernelException() (priority: 0) and let Symfony log the exception.
     */
    #[AsEventListener(event: KernelEvents::EXCEPTION, priority: -10)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $this->exceptionSentryReporter->handle($exception, handled: false);

        if (!is_null($apiErrorResponse = $this->createApiErrorResponse($event->getRequest(), $exception))) {
            $event->setResponse($apiErrorResponse);
        }
    }

    private function createApiErrorResponse(Request $request, Throwable $exception): ?JsonResponse
    {
        $isApiRoute = $request->attributes->get('_api');

        if ($isApiRoute !== true) {
            return null;
        }

        $exception = $this->httpExceptionMapper->map($exception);

        return new JsonResponse(
            data: [
                'error' => [
                    'message' => $exception->getMessage(),
                ],
            ],
            status: $exception->getStatusCode(),
            headers: $exception->getHeaders(),
        );
    }
}
