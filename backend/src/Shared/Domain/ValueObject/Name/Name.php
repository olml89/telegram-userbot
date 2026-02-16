<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\StringLengthException;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\StringValueObject;

final readonly class Name extends StringValueObject
{
    private const int MIN_LENGTH = 1;
    private const int MAX_LENGTH = 50;

    public static function maxLength(): int
    {
        return self::MAX_LENGTH;
    }

    /**
     * @throws StringLengthException
     */
    protected static function validate(string $value): void
    {
        if (mb_strlen($value) < self::MIN_LENGTH || mb_strlen($value) > self::MAX_LENGTH) {
            throw StringLengthException::between(self::MIN_LENGTH, self::MAX_LENGTH);
        }
    }
}
