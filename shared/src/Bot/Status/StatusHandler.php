<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Status;

interface StatusHandler
{
    public function handle(Status $status): void;
}
