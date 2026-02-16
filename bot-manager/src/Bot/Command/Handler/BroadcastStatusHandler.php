<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Handler;

use olml89\TelegramUserbot\BotManager\Bot\Command\Command;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\BroadcastStatusCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\HandlesCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\InvalidCommandException;
use olml89\TelegramUserbot\BotManager\Bot\Status\StatusManager;

#[HandlesCommand(BroadcastStatusCommand::class)]
final readonly class BroadcastStatusHandler implements CommandHandler
{
    public function __construct(
        private StatusManager $statusManager,
    ) {}

    /**
     * @throws InvalidCommandException
     */
    public function handle(Command $command): void
    {
        BroadcastStatusCommand::validate($command);

        $this->statusManager->emit();
    }
}
