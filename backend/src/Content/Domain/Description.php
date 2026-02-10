<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\StringLengthException;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\StringValueObject;

final readonly class Description extends StringValueObject
{
    private const int MIN_LENGTH = 1;

    /**
     * @throws StringLengthException
     */
    protected static function validate(string $value): void
    {
        if (mb_strlen($value) < self::MIN_LENGTH) {
            throw StringLengthException::tooShort(self::MIN_LENGTH);
        }
    }
}
