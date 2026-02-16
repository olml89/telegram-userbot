<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\EventSubscriber;

use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception\ExceptionSentryReporter;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class SentryExceptionReportingSubscriber
{
    public function __construct(
        private ExceptionSentryReporter $exceptionSentryReporter,
    ) {
    }

    #[AsEventListener(event: KernelEvents::EXCEPTION, priority: 0)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $this->exceptionSentryReporter->handle($event->getThrowable(), handled: false);
    }
}
