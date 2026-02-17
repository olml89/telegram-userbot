<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception;

use BackedEnum;
use Exception;

final class UnsupportedStringValue extends Exception
{
    /**
     * @param class-string<BackedEnum> $enumClass
     */
    public function __construct(string $enumClass, string $value)
    {
        parent::__construct(
            sprintf(
                'Unsupported %s (%s)',
                $enumClass,
                $value,
            ),
        );
    }
}
