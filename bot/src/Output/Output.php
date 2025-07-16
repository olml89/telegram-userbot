<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Output;

use Stringable;

interface Output extends Stringable
{
    public function isBroadcastable(): bool;
}
