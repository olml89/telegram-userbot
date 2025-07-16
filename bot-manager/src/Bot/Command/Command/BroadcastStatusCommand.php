<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Command;

use olml89\TelegramUserbot\BotManager\Bot\Command\BaseCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandType;

final readonly class BroadcastStatusCommand extends BaseCommand implements Command
{
    public function __construct()
    {
        parent::__construct(CommandType::BroadcastStatus);
    }

    public function handle(CommandHandler $commandHandler): void
    {
        $commandHandler->broadcastStatus();
    }
}
