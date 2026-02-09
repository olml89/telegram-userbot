<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;
use Stringable;

abstract readonly class StringValueObject implements Stringable
{
    public function __construct(
        public string $value,
    ) {
        static::validate($value);
    }

    /** @throws InvariantException */
    abstract protected static function validate(string $value): void;

    public function __toString(): string
    {
        return $this->value;
    }
}
