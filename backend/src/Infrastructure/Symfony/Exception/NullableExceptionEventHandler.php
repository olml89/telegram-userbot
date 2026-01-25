<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Exception;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final readonly class NullableExceptionEventHandler implements ExceptionEventHandler
{
    public function handle(ExceptionEvent $event): void
    {
        /**
         * We don't need to do anything here, as WebProfiler is automatically logging exceptions and events
         * on development environment.
         */
    }
}
