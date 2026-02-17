<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\Status;

use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\IsSerializableStringBackedEnum;
use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\SafeStringBackedEnum;
use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\SerializableStringBackedEnum;

enum Status: string implements SerializableStringBackedEnum, SafeStringBackedEnum
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

    /**
     * @throws UnsupportedStatusException
     */
    public static function create(string $value): self
    {
        return self::tryFrom($value) ?? throw new UnsupportedStatusException($value);
    }
}
