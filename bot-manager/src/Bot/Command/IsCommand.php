<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use olml89\TelegramUserbot\Application\IsJsonSerializable;
use olml89\TelegramUserbot\Application\IsStringable;

/**
 * @mixin Command
 */
trait IsCommand
{
    use IsJsonSerializable;
    use IsStringable;

    private readonly CommandType $type;
}
