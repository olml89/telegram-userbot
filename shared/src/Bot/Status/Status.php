<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Status;

use JsonSerializable;
use Stringable;

interface Status extends JsonSerializable, Stringable
{
    public function withMessage(null|string|Stringable $message = null): Status;

    /**
     * @throws InvalidStatusException
     */
    public function assertEquals(StatusType ...$expectedStatusTypes): void;
}
