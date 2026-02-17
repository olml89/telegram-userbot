<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\Language;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\UnsupportedStringValueException;

final class UnsupportedLanguageException extends UnsupportedStringValueException
{
    public static function enumName(): string
    {
        return 'language';
    }
}
