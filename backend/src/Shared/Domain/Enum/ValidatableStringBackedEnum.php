<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Enum;

interface ValidatableStringBackedEnum
{
    /** @return string[] */
    public static function values(): array;
}
