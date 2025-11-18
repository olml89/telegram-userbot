<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Command;

use olml89\TelegramUserbot\BotManager\Bot\Command\Command;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandType;
use olml89\TelegramUserbot\BotManager\Bot\Command\IsCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\ProcessableCommand;
use olml89\TelegramUserbot\Shared\Bot\Process\Process;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessType;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStartedException;

final readonly class RequestStatusCommand implements Command, ProcessableCommand
{
    use IsCommand;

    public function __construct()
    {
        $this->type = CommandType::RequestStatus;
    }

    public function process(): Process
    {
        return new Process(ProcessType::RequestStatus);
    }

    /**
     * @throws ProcessNotStartedException
     */
    public function handle(CommandHandler $commandHandler): void
    {
        $commandHandler->requestStatus($this);
    }
}
