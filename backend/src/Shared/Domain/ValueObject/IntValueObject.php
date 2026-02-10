<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

abstract readonly class IntValueObject
{
    public function __construct(
        public int $value,
    ) {
        static::validate($value);
    }

    /** @throws InvariantException */
    abstract protected static function validate(int $value): void;
}
