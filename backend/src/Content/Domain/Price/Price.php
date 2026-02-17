<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\Price;

use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\FloatValueObject;

final readonly class Price extends FloatValueObject
{
    private const float MIN_VALUE = 0.0;

    /**
     * @throws PriceException
     */
    protected static function validate(float $value): void
    {
        if ($value < self::MIN_VALUE) {
            throw new PriceException(self::MIN_VALUE);
        }
    }
}
