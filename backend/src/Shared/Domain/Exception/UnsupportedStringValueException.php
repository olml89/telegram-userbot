<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception;

use Exception;

abstract class UnsupportedStringValueException extends Exception
{
    public function __construct(string $value)
    {
        parent::__construct(
            sprintf(
                'Unsupported %s (%s)',
                static::enumName(),
                $value,
            ),
        );
    }

    abstract protected static function enumName(): string;
}
