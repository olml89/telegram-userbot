<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\Mode;

use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\IsSerializableStringBackedEnum;
use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\SafeStringBackedEnum;
use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\SerializableStringBackedEnum;

enum Mode: string implements SerializableStringBackedEnum, SafeStringBackedEnum
{
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

    /**
     * @throws UnsupportedModeException
     */
    public static function create(string $value): self
    {
        return self::tryFrom($value) ?? throw new UnsupportedModeException($value);
    }
}
