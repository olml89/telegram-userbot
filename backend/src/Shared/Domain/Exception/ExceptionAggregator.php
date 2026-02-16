<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception;

use Throwable;

interface ExceptionAggregator
{
    public function aggregateException(Throwable $exception): void;

    /** @return Throwable[] */
    public function getAggregatedExceptions(): array;
}
