<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use olml89\TelegramUserbot\Shared\App\IsJsonSerializable;
use olml89\TelegramUserbot\Shared\App\IsStringable;

/**
 * @mixin Command
 */
trait IsCommand
{
    use IsJsonSerializable;
    use IsStringable;

    private readonly CommandType $type;
}
