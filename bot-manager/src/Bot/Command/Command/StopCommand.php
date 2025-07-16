<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Command;

use olml89\TelegramUserbot\BotManager\Bot\Command\BaseProcessableCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandType;
use olml89\TelegramUserbot\Shared\Bot\Process\Process;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStoppedException;
use olml89\TelegramUserbot\Shared\Bot\Status\InvalidStatusException;

final readonly class StopCommand extends BaseProcessableCommand implements Command
{
    public function __construct()
    {
        parent::__construct(CommandType::Stop, Process::Runner);
    }

    /**
     * @throws InvalidStatusException
     * @throws ProcessNotStoppedException
     */
    public function handle(CommandHandler $commandHandler): void
    {
        $commandHandler->stop($this);
    }
}
