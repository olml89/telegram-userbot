<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use olml89\TelegramUserbot\Shared\Bot\Process\Process;

interface ProcessableCommand
{
    public function process(): Process;
}
