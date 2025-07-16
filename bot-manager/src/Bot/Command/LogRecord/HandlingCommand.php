<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command\LogRecord;

use olml89\TelegramUserbot\BotManager\Bot\Command\Command;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\InfoLogRecord;

final readonly class HandlingCommand extends InfoLogRecord
{
    public Command $command;

    public function __construct(Command $command)
    {
        parent::__construct(message: 'Handling command');

        $this->command = $command;
    }

    protected function context(): array
    {
        return [
            'command' => $this->command,
        ];
    }
}
