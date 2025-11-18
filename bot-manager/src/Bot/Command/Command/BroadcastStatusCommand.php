<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Command;

use olml89\TelegramUserbot\BotManager\Bot\Command\Command;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandType;
use olml89\TelegramUserbot\BotManager\Bot\Command\IsCommand;

final readonly class BroadcastStatusCommand implements Command
{
    use IsCommand;

    public function __construct()
    {
        $this->type = CommandType::BroadcastStatus;
    }

    public function handle(CommandHandler $commandHandler): void
    {
        $commandHandler->broadcastStatus();
    }
}
