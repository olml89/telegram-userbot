<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Duration;

use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\FloatValueObject;

final readonly class Duration extends FloatValueObject
{
    /**
     * @throws DurationException
     */
    protected static function validate(float $value): void
    {
        if ($value <= 0.0) {
            throw new DurationException();
        }
    }
}
