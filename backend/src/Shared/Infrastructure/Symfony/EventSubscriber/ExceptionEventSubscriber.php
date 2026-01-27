<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\EventSubscriber;

use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception\ExceptionSentryReporter;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class ExceptionEventSubscriber
{
    public function __construct(
        private ExceptionSentryReporter $exceptionSentryReporter,
    ) {
    }

    #[AsEventListener(event: KernelEvents::EXCEPTION, priority: 128)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $this->exceptionSentryReporter->handle($exception, handled: false);
    }
}
