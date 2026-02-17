<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Duration;

use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\FloatValueObject;

final readonly class Duration extends FloatValueObject
{
    private const int MIN_VALUE = 0;

    /**
     * @throws DurationException
     */
    protected static function validate(float $value): void
    {
        if ($value < self::MIN_VALUE) {
            throw DurationException::tooLow(self::MIN_VALUE);
        }
    }
}
