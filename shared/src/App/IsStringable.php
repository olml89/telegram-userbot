<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\App;

use JsonException;
use Stringable;

/**
 * @mixin Stringable
 */
trait IsStringable
{
    /**
     * @throws JsonException
     */
    public function __toString(): string
    {
        return json_encode($this->jsonSerialize(), flags: JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }
}
