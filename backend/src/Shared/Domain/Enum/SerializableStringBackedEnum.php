<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Enum;

use JsonSerializable;

interface SerializableStringBackedEnum extends JsonSerializable
{
    public function label(): string;
}
