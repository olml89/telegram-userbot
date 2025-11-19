<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Handler;

use olml89\TelegramUserbot\BotManager\Bot\Command\Command;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\RequestStatusCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\HandlesCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\InvalidCommandException;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessManager;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStartedException;
use olml89\TelegramUserbot\Shared\Bot\Process\Process;

#[HandlesCommand(RequestStatusCommand::class)]
final readonly class RequestStatusHandler implements CommandHandler
{
    public function __construct(
        private ProcessManager $processManager,
    ) {
    }

    /**
     * @throws InvalidCommandException
     * @throws ProcessNotStartedException
     */
    public function handle(Command $command): void
    {
        RequestStatusCommand::validate($command);

        $this->processManager->start(Process::RequestStatus);
    }
}
