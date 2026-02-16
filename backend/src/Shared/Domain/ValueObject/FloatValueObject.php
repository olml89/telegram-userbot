<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

abstract readonly class FloatValueObject
{
    public function __construct(
        public float $value,
    ) {
        static::validate($value);
    }

    /** @throws InvariantException */
    abstract protected static function validate(float $value): void;
}
