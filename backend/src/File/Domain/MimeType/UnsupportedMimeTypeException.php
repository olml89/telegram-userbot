<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\MimeType;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\UnsupportedStringValueException;

final class UnsupportedMimeTypeException extends UnsupportedStringValueException
{
    protected static function enumName(): string
    {
        return 'mimeType';
    }
}
