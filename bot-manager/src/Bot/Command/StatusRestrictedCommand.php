<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use olml89\TelegramUserbot\Shared\Bot\Status\InvalidStatusException;
use olml89\TelegramUserbot\Shared\Bot\Status\Status;

interface StatusRestrictedCommand
{
    /**
     * @throws InvalidStatusException
     */
    public function checkAllowedBy(Status $status): void;
}
