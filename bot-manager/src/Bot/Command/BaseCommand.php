<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use olml89\TelegramUserbot\Shared\App\IsJsonSerializable;
use olml89\TelegramUserbot\Shared\App\IsStringable;

abstract readonly class BaseCommand
{
    use IsJsonSerializable;
    use IsStringable;

    public CommandType $type;

    public function __construct(CommandType $type)
    {
        $this->type = $type;
    }
}
