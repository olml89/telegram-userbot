<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\Price;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\IsOutOfRangeException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

final class PriceException extends InvariantException
{
    use IsOutOfRangeException;

    public function __construct(float $minValue)
    {
        parent::__construct(self::tooLow($minValue));
    }
}
