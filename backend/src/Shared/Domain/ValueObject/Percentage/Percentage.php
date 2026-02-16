<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Percentage;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\OutOfRangeException;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\IntValueObject;

final readonly class Percentage extends IntValueObject
{
    private const int MIN_VALUE = 1;
    private const int MAX_VALUE = 100;

    /**
     * @throws OutOfRangeException
     */
    protected static function validate(int $value): void
    {
        if ($value < self::MIN_VALUE || $value > self::MAX_VALUE) {
            throw OutOfRangeException::between(self::MIN_VALUE, self::MAX_VALUE);
        }
    }
}
