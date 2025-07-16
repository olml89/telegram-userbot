<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Status;

use olml89\TelegramUserbot\Shared\Bot\Status\Status;
use olml89\TelegramUserbot\Shared\Bot\Status\Status\DisconnectedStatus;

final class StatusVault
{
    private Status $status;

    public function __construct()
    {
        $this->status = new DisconnectedStatus();
    }

    public function get(): Status
    {
        return $this->status;
    }

    public function set(Status $status): void
    {
        $this->status = $status;
    }
}
