<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Command;

use olml89\TelegramUserbot\BotManager\Bot\Command\CommandType;
use olml89\TelegramUserbot\BotManager\Bot\Command\IsUnrestrictedCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\UnrestrictedCommand;

final readonly class RequestStatusCommand implements UnrestrictedCommand
{
    use IsUnrestrictedCommand;

    public function __construct()
    {
        $this->type = CommandType::RequestStatus;
    }
}
