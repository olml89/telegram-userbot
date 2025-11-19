<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

interface UnrestrictedCommand extends Command
{
    /**
     * @throws InvalidCommandException
     */
    public static function validate(Command $command): static;
}
