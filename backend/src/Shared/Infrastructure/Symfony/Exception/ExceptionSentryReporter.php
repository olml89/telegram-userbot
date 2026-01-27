<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception;

use olml89\TelegramUserbot\Backend\Shared\Domain\ExceptionHandler;
use Sentry\State\HubInterface;
use Throwable;

final readonly class ExceptionSentryReporter implements ExceptionHandler
{
    use ItFiltersCriticalExceptions;

    public function __construct(
        private HubInterface $sentry,
    ) {
    }

    public function handle(Throwable $exception): void
    {
        if ($this->isCritical($exception)) {
            $this->sentry->captureException($exception);
        }
    }
}
