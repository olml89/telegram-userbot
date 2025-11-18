<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Command;

use olml89\TelegramUserbot\BotManager\Bot\Command\Command;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandType;
use olml89\TelegramUserbot\BotManager\Bot\Command\IsCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\IsStatusRestrictedCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\ProcessableCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\StatusRestrictedCommand;
use olml89\TelegramUserbot\Shared\Bot\Process\Process;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessType;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStartedException;
use olml89\TelegramUserbot\Shared\Bot\Status\InvalidStatusException;
use olml89\TelegramUserbot\Shared\Bot\Status\StatusType;

final readonly class StartCommand implements Command, ProcessableCommand, StatusRestrictedCommand
{
    use IsCommand;
    use IsStatusRestrictedCommand;

    public function __construct()
    {
        $this->type = CommandType::Start;
    }

    public function process(): Process
    {
        return new Process(ProcessType::Runner);
    }

    /**
     * @return StatusType[]
     */
    protected function allowedStatusTypes(): array
    {
        return [
            StatusType::LoggedIn,
        ];
    }

    /**
     * @throws InvalidStatusException
     * @throws ProcessNotStartedException
     */
    public function handle(CommandHandler $commandHandler): void
    {
        $commandHandler->start($this);
    }
}
