<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\EventSubscriber;

use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception\ApiExceptionResponseMapper;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class ApiExceptionResponseSubscriber
{
    public function __construct(
        private ApiExceptionResponseMapper $exceptionJsonResponseMapper,
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
        $request = $event->getRequest();

        if (!is_null($apiErrorResponse = $this->exceptionJsonResponseMapper->map($request, $exception))) {
            $event->setResponse($apiErrorResponse);
        }
    }
}
