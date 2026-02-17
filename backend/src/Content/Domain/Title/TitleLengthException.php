<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\Title;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\IsStringLengthException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

final class TitleLengthException extends InvariantException
{
    use IsStringLengthException;

    public function __construct(int $minLength, int $maxLength)
    {
        parent::__construct(self::between($minLength, $maxLength));
    }
}
