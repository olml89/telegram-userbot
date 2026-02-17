<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Http\Api\Validate;

use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\IsValidatableStringBackedEnum;
use olml89\TelegramUserbot\Backend\Shared\Domain\Enum\ValidatableStringBackedEnum;

enum EventType: string implements ValidatableStringBackedEnum
{
    use IsValidatableStringBackedEnum;

    case PreCreate = 'pre-create';
}
