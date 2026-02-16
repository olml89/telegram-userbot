<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

final class StringLengthException extends InvariantException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function between(int $minLength, int $maxLength): self
    {
        return new self(
            sprintf(
                'Value must be between %d and %d characters',
                $minLength,
                $maxLength,
            ),
        );
    }

    public static function tooShort(int $minLength): self
    {
        return new self(
            sprintf(
                'Value cannot be shorter than %d characters',
                $minLength,
            ),
        );
    }

    public static function tooLong(int $maxLength): self
    {
        return new self(
            sprintf(
                'Value cannot be longer than %d characters',
                $maxLength,
            ),
        );
    }
}
