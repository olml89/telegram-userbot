<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Status\Status;

use olml89\TelegramUserbot\Shared\Bot\Status\IsStatus;
use olml89\TelegramUserbot\Shared\Bot\Status\Status;
use olml89\TelegramUserbot\Shared\Bot\Status\StatusType;

final readonly class NotLoggedInStatus implements Status
{
    use IsStatus;

    protected function type(): StatusType
    {
        return StatusType::NotLoggedIn;
    }
}
