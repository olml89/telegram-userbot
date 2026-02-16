<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Monolog;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\ExceptionAggregator;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\ExceptionChainBuilder;
use Throwable;

#[AsMonologProcessor]
final readonly class ExceptionAggregatorProcessor
{
    public function __construct(
        private ExceptionChainBuilder $exceptionChainBuilder,
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $exception = $record->context['exception'] ?? null;

        if ($exception instanceof ExceptionAggregator) {
            $aggregatedExceptions = array_map(
                fn (Throwable $aggregatedException): array => $this->exceptionChainBuilder->build($aggregatedException),
                $exception->getAggregatedExceptions(),
            );

            $record = $record->with(
                context: $record->context + [
                    'aggregatedExceptions' => $aggregatedExceptions,
                ],
            );
        }

        return $record;
    }
}
