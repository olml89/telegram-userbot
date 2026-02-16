<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Handler;

use olml89\TelegramUserbot\BotManager\Bot\Command\Command;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\LogoutCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\HandlesCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\InvalidCommandException;
use olml89\TelegramUserbot\BotManager\Bot\Status\StatusManager;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessManager;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStartedException;
use olml89\TelegramUserbot\Shared\Bot\Process\Process;
use olml89\TelegramUserbot\Shared\Bot\Status\InvalidStatusException;

#[HandlesCommand(LogoutCommand::class)]
final readonly class LogoutHandler implements CommandHandler
{
    public function __construct(
        private StatusManager $statusManager,
        private ProcessManager $processManager,
    ) {}

    /**
     * @throws InvalidCommandException
     * @throws InvalidStatusException
     * @throws ProcessNotStartedException
     */
    public function handle(Command $command): void
    {
        LogoutCommand::validate($command, $this->statusManager->status());

        $this->processManager->start(Process::Logout);
    }
}
