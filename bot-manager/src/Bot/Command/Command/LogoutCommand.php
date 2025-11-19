<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Command;

use olml89\TelegramUserbot\BotManager\Bot\Command\CommandType;
use olml89\TelegramUserbot\BotManager\Bot\Command\IsStatusRestrictedCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\StatusRestrictedCommand;
use olml89\TelegramUserbot\Shared\Bot\Status\StatusType;

final readonly class LogoutCommand implements StatusRestrictedCommand
{
    use IsStatusRestrictedCommand;

    public function __construct()
    {
        $this->type = CommandType::Logout;
    }

    /**
     * @return StatusType[]
     */
    protected static function allowedStatusTypes(): array
    {
        return [
            StatusType::LoggedIn,
        ];
    }
}
