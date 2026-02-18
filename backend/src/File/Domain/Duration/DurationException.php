<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Duration;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

final class DurationException extends InvariantException
{
    public function __construct()
    {
        parent::__construct('Duration must be greater than 0 seconds');
    }
}
