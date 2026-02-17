<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Enum;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\UnsupportedStringValueException;

interface SafeStringBackedEnum
{
    /** @throws UnsupportedStringValueException */
    public static function create(string $value): self;
}
