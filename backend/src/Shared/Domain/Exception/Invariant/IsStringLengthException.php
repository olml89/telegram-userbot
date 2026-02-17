<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

/**
 * @mixin InvariantException
 */
trait IsStringLengthException
{
    private static function between(int $minLength, int $maxLength): string
    {
        return sprintf(
            'Value must be between %d and %d characters',
            $minLength,
            $maxLength,
        );
    }

    private static function tooShort(int $minLength): string
    {
        return sprintf(
            'Value cannot be shorter than %d characters',
            $minLength,
        );
    }

    private static function tooLong(int $maxLength): string
    {
        return sprintf(
            'Value cannot be longer than %d characters',
            $maxLength,
        );
    }
}
