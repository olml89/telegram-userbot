<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use JsonSerializable;
use Stringable;

interface Command extends JsonSerializable, Stringable
{
    public function handle(CommandHandler $commandHandler): void;
}
