<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\IsSerializableStringBackedEnum;
use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\IsValidatableStringBackedEnum;
use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\SerializableStringBackedEnum;
use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\ValidatableStringBackedEnum;

enum Language: string implements ValidatableStringBackedEnum, SerializableStringBackedEnum
{
    use IsValidatableStringBackedEnum;
    use IsSerializableStringBackedEnum;

    case Catalan = 'ca';
    case English = 'en';
    case Spanish = 'es';
    case Portuguese = 'pt';

    public function label(): string
    {
        return match ($this) {
            self::Catalan => 'Catalan',
            self::English => 'English',
            self::Spanish => 'Spanish',
            self::Portuguese => 'Portuguese',
        };
    }
}
