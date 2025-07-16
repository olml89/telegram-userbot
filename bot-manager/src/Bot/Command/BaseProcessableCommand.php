<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use olml89\TelegramUserbot\Shared\Bot\Process\Process;

abstract readonly class BaseProcessableCommand extends BaseCommand
{
    public Process $process;

    public function __construct(CommandType $type, Process $process)
    {
        parent::__construct($type);

        $this->process = $process;
    }
}
