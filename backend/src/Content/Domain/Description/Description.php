<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\Description;

use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\StringValueObject;

final readonly class Description extends StringValueObject
{
    private const int MIN_LENGTH = 1;

    /**
     * @throws DescriptionLengthException
     */
    protected static function validate(string $value): void
    {
        if (mb_strlen($value) < self::MIN_LENGTH) {
            throw new DescriptionLengthException(self::MIN_LENGTH);
        }
    }
}
