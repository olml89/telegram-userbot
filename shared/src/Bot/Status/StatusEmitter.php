<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Status;

interface StatusEmitter
{
    public function emit(Status $status): void;
}
