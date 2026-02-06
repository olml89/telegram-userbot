<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\IsSerializableStringBackedEnum;
use olml89\TelegramUserbot\Backend\Shared\Domain\SerializableStringBackedEnum;

enum Status: string implements SerializableStringBackedEnum
{
    use IsSerializableStringBackedEnum;

    case Active = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
        };
    }
}
