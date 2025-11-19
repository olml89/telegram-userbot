<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

/**
 * @mixin UnrestrictedCommand
 */
trait IsUnrestrictedCommand
{
    use IsCommand;

    /**
     * @throws InvalidCommandException
     */
    public static function validate(Command $command): static
    {
        if (!$command instanceof static) {
            throw new InvalidCommandException($command, static::class);
        }

        return $command;
    }
}
