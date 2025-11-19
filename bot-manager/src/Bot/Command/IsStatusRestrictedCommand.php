<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use olml89\TelegramUserbot\Shared\Bot\Status\InvalidStatusException;
use olml89\TelegramUserbot\Shared\Bot\Status\Status;
use olml89\TelegramUserbot\Shared\Bot\Status\StatusType;

/**
 * @mixin StatusRestrictedCommand
 */
trait IsStatusRestrictedCommand
{
    use IsCommand;

    /**
     * @return StatusType[]
     */
    abstract protected static function allowedStatusTypes(): array;

    /**
     * @throws InvalidCommandException
     * @throws InvalidStatusException
     */
    public static function validate(Command $command, Status $status): static
    {
        if (!$command instanceof static) {
            throw new InvalidCommandException($command, static::class);
        }

        if (!in_array($status->type, self::allowedStatusTypes(), strict: true)) {
            throw new InvalidStatusException($status->type, ...self::allowedStatusTypes());
        }

        return $command;
    }
}
