<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Status;

use Exception;

final class InvalidStatusTypeException extends Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function missingType(): self
    {
        return new self('Status type is missing');
    }

    public static function notString(): self
    {
        return new self('Invalid status type: not a string');
    }

    public static function invalidType(string $invalidTypeString): self
    {
        return new self(sprintf(
            'Invalid status type: %s, valid status types: %s',
            $invalidTypeString,
            implode(
                separator: ', ',
                array: array_map(
                    fn(StatusType $type): string => $type->value,
                    StatusType::cases(),
                ),
            ),
        ));
    }
}
