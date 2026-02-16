<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Enum;

use BackedEnum;

/**
 * @mixin BackedEnum
 * @mixin ValidatableStringBackedEnum
 */
trait IsValidatableStringBackedEnum
{
    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(
            fn(self $enum): string => $enum->value,
            self::cases(),
        );
    }
}
