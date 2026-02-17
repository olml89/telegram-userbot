<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

/**
 * @mixin InvariantException
 */
trait IsOutOfRangeException
{
    private static function between(int|float $minValue, int|float $maxValue): string
    {
        return sprintf(
            'Value must be between %d and %d',
            $minValue,
            $maxValue,
        );
    }

    private static function tooLow(int|float $minValue): string
    {
        return sprintf(
            'Value must be greater or equal than %d',
            $minValue,
        );
    }

    private static function tooHigh(int|float $maxValue): string
    {
        return sprintf(
            'Value must be lower or equal than %d',
            $maxValue,
        );
    }
}
