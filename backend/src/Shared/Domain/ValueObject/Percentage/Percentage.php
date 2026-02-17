<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Percentage;

use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\IntValueObject;

final readonly class Percentage extends IntValueObject
{
    /**
     * @throws PercentageException
     */
    protected static function validate(int $value): void
    {
        if ($value < 1 || $value > 100) {
            throw new PercentageException();
        }
    }
}
