<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Command;

use olml89\TelegramUserbot\BotManager\Bot\Command\BaseProcessableCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandType;
use olml89\TelegramUserbot\Shared\Bot\Process\Process;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStartedException;

final readonly class RequestStatusCommand extends BaseProcessableCommand implements Command
{
    public function __construct()
    {
        parent::__construct(CommandType::RequestStatus, Process::RequestStatus);
    }

    /**
     * @throws ProcessNotStartedException
     */
    public function handle(CommandHandler $commandHandler): void
    {
        $commandHandler->requestStatus($this);
    }
}
