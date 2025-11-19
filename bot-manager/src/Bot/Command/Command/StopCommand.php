<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\Command;

use olml89\TelegramUserbot\BotManager\Bot\Command\CommandType;
use olml89\TelegramUserbot\BotManager\Bot\Command\IsStatusRestrictedCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\StatusRestrictedCommand;
use olml89\TelegramUserbot\Shared\Bot\Status\StatusType;

final readonly class StopCommand implements StatusRestrictedCommand
{
    use IsStatusRestrictedCommand;

    public function __construct()
    {
        $this->type = CommandType::Stop;
    }

    /**
     * @return StatusType[]
     */
    protected static function allowedStatusTypes(): array
    {
        return [
            StatusType::Running,
        ];
    }
}
