<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use olml89\TelegramUserbot\Shared\Bot\Status\InvalidStatusException;
use olml89\TelegramUserbot\Shared\Bot\Status\Status;

interface StatusRestrictedCommand extends Command
{
    /**
     * @throws InvalidCommandException
     * @throws InvalidStatusException
     */
    public static function validate(Command $command, Status $status): static;
}
