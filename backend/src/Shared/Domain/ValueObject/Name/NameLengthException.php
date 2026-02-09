<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

final class NameLengthException extends InvariantException
{
    public static function tooShort(int $minLength): self
    {
        return new self(
            sprintf(
                'The tag name cannot be shorter than %d characters',
                $minLength,
            ),
        );
    }

    public static function tooLong(int $maxLength): self
    {
        return new self(
            sprintf(
                'The tag name cannot be longer than %d characters',
                $maxLength,
            ),
        );
    }
}
