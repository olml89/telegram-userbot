<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception;

use Throwable;

interface ExceptionHandler
{
    public function handle(Throwable $exception, bool $handled = true): void;
}
