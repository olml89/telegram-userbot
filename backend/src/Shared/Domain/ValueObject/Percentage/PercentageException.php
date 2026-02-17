<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Percentage;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\IsOutOfRangeException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

final class PercentageException extends InvariantException
{
    use IsOutOfRangeException;

    public function __construct()
    {
        parent::__construct(self::between(1, 100));
    }
}
