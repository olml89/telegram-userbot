<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use Exception;

final class InvalidCommandException extends Exception
{
    /**
     * @param class-string<Command> $expectedCommandClass
     */
    public function __construct(Command $invalidCommand, string $expectedCommandClass)
    {
        parent::__construct(
            sprintf(
                'Invalid command: %s, expected command: %s',
                $invalidCommand::class,
                $expectedCommandClass,
            ),
        );
    }
}
