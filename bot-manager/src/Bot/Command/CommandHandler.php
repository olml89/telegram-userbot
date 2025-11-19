<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

interface CommandHandler
{
    public function handle(Command $command): void;
}
