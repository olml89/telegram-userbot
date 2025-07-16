<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Command;

use olml89\TelegramUserbot\BotManager\Bot\Command\BaseProcessableCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandType;
use olml89\TelegramUserbot\Shared\Bot\Process\Process;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStartedException;
use olml89\TelegramUserbot\Shared\Bot\Status\InvalidStatusException;

final readonly class StartCommand extends BaseProcessableCommand implements Command
{
    public function __construct()
    {
        parent::__construct(CommandType::Start, Process::Runner);
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
