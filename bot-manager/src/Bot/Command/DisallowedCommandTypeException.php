<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use Exception;

final class DisallowedCommandTypeException extends Exception
{
    public function __construct(CommandType $type)
    {
        parent::__construct(sprintf(
            'Disallowed command type: %s',
            $type->value,
        ));
    }
}
