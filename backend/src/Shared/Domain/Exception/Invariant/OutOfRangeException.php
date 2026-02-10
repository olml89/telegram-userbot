<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

final class OutOfRangeException extends InvariantException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function between(int|float $minValue, int|float $maxValue): self
    {
        return new self(
            sprintf(
                'Value must be between %d and %d',
                $minValue,
                $maxValue,
            ),
        );
    }

    public static function tooLow(int|float $minValue): self
    {
        return new self(
            sprintf(
                'Value must be greater or equal than %d',
                $minValue,
            ),
        );
    }

    public static function tooHigh(int|float $maxValue): self
    {
        return new self(
            sprintf(
                'Value must be lower or equal than %d',
                $maxValue,
            ),
        );
    }
}
