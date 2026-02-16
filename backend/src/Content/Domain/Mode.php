<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\IsSerializableStringBackedEnum;
use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\IsValidatableStringBackedEnum;
use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\SerializableStringBackedEnum;
use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\ValidatableStringBackedEnum;

enum Mode: string implements ValidatableStringBackedEnum, SerializableStringBackedEnum
{
    use IsValidatableStringBackedEnum;
    use IsSerializableStringBackedEnum;

    case Selling = 'selling';
    case Teasing = 'teasing';

    public function label(): string
    {
        return match ($this) {
            self::Selling => 'Selling',
            self::Teasing => 'Teasing',
        };
    }
}
