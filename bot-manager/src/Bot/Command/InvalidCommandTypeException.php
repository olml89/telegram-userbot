<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use Exception;

final class InvalidCommandTypeException extends Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function missingType(): self
    {
        return new self('Command type is missing');
    }

    public static function notString(): self
    {
        return new self('Invalid command type: not a string');
    }

    public static function invalidType(string $invalidTypeString): self
    {
        return new self(sprintf(
            'Invalid command type: %s, valid command types: %s',
            $invalidTypeString,
            implode(
                separator: ', ',
                array: array_map(
                    fn (CommandType $type): string => $type->value,
                    CommandType::cases(),
                ),
            ),
        ));
    }
}
