<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Duration;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\IsOutOfRangeException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

final class DurationException extends InvariantException
{
    use IsOutOfRangeException {
        tooLow as durationTooLow;
    }

    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function tooLow(float $minValue): self
    {
        return new self(self::durationTooLow($minValue));
    }

    public static function invalid(): self
    {
        return new self('Invalid duration');
    }

    public static function missing(): self
    {
        return new self('Could not extract duration from file');
    }
}
