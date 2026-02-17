<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\Title;

use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\StringValueObject;

final readonly class Title extends StringValueObject
{
    private const int MIN_LENGTH = 1;
    private const int MAX_LENGTH = 255;

    public static function maxLength(): int
    {
        return self::MAX_LENGTH;
    }

    /**
     * @throws TitleLengthException
     */
    protected static function validate(string $value): void
    {
        if (mb_strlen($value) < self::MIN_LENGTH || mb_strlen($value) > self::MAX_LENGTH) {
            throw new TitleLengthException(self::MIN_LENGTH, self::MAX_LENGTH);
        }
    }
}
