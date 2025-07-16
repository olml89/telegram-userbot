<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Output;

use Throwable;

final readonly class ExceptionOutput implements Output
{
    public function __construct(
        private Throwable $throwable,
    ) {
    }

    public function isBroadcastable(): bool
    {
        return true;
    }

    public function __toString(): string
    {
        return (string)$this->throwable;
    }
}
