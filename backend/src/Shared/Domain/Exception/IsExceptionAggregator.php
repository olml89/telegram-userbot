<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception;

use Throwable;

/**
 * @mixin ExceptionAggregator
 */
trait IsExceptionAggregator
{
    /**
     * @var Throwable[]
     */
    protected array $aggregatedExceptions = [];

    public function aggregateException(Throwable $exception): void
    {
        $this->aggregatedExceptions[] = $exception;
    }

    /**
     * @return Throwable[]
     */
    public function getAggregatedExceptions(): array
    {
        return $this->aggregatedExceptions;
    }
}
