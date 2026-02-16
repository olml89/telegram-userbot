<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Size;

use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\IntValueObject;

final readonly class Size extends IntValueObject
{
    private const int MAX_SIZE = 3221225472; # 3 GB

    /**
     * @throws SizeException
     */
    protected static function validate(int $value): void
    {
        if ($value <= 0) {
            throw SizeException::empty();
        }

        if ($value > self::MAX_SIZE) {
            throw SizeException::tooBig(self::MAX_SIZE);
        }
    }
}
