<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\ExceptionAggregator;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Handler\ExceptionHandler;
use Sentry\EventHint;
use Sentry\ExceptionMechanism;
use Sentry\State\HubInterface;
use Throwable;

final readonly class ExceptionSentryReporter implements ExceptionHandler
{
    use ItFiltersCriticalExceptions;

    public function __construct(
        private HubInterface $sentry,
    ) {}

    public function handle(Throwable $exception, bool $handled = true): void
    {
        if (!$this->isCritical($exception)) {
            return;
        }

        $this->reportException($exception, $handled);

        if ($exception instanceof ExceptionAggregator) {
            foreach ($exception->getAggregatedExceptions() as $aggregateException) {
                $this->reportException($aggregateException, $handled);
            }
        }
    }

    private function reportException(Throwable $exception, bool $handled): void
    {
        $hint = EventHint::fromArray([
            'exception' => $exception,
            'mechanism' => new ExceptionMechanism(ExceptionMechanism::TYPE_GENERIC, handled: $handled),
        ]);

        $this->sentry->captureException($exception, $hint);
    }
}
