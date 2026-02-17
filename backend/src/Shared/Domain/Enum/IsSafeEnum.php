<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Enum;

use BackedEnum;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\UnsupportedStringValue;

/**
 * @mixin BackedEnum
 */
trait IsSafeEnum
{
    /**
     * @throws UnsupportedStringValue
     */
    public static function create(string $value): static
    {
        return static::tryFrom($value) ?? throw new UnsupportedStringValue(static::class, $value);
    }
}
