<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\IsSerializableStringBackedEnum;
use olml89\TelegramUserbot\Backend\Shared\Domain\SerializableStringBackedEnum;

enum Language: string implements SerializableStringBackedEnum
{
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
