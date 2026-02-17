<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\Status;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\UnsupportedStringValueException;

final class UnsupportedStatusException extends UnsupportedStringValueException
{
    protected static function enumName(): string
    {
        return 'status';
    }
}
