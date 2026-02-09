<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name;

use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\StringValueObject;

final readonly class Name extends StringValueObject
{
    private const int MIN_LENGTH = 1;
    private const int MAX_LENGTH = 50;

    /**
     * @throws NameLengthException
     */
    protected static function validate(string $value): void
    {
        if (mb_strlen($value) < self::MIN_LENGTH) {
            throw NameLengthException::tooShort(self::MIN_LENGTH);
        }

        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw NameLengthException::tooLong(self::MAX_LENGTH);
        }
    }
}
