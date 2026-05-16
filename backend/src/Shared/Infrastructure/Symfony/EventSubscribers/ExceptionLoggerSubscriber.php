<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\EventSubscribers;

use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception\ExceptionLogger;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * In PROD: logs exceptions via ExceptionHandlerPipeline (which includes ExceptionLogger + ExceptionSentryReporter)
 * In DEV: Symfony's ErrorListener handles logging automatically (higher priority)
 */
final readonly class ExceptionLoggerSubscriber
{
    public function __construct(
        private ExceptionLogger $exceptionLogger,
    ) {}

    /**
     * Priority: 1 (after ErrorListener -128, but only registered in PROD)
     */
    #[AsEventListener(event: KernelEvents::EXCEPTION, priority: 1)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $this->exceptionLogger->handle($event->getThrowable(), handled: false);
    }
}
