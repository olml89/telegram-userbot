<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\Description;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\IsStringLengthException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

final class DescriptionLengthException extends InvariantException
{
    use IsStringLengthException;

    public function __construct(int $minLength)
    {
        parent::__construct(self::tooShort($minLength));
    }
}
