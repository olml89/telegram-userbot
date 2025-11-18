<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use olml89\TelegramUserbot\Shared\Bot\Status\InvalidStatusException;
use olml89\TelegramUserbot\Shared\Bot\Status\Status;
use olml89\TelegramUserbot\Shared\Bot\Status\StatusType;

/**
 * @mixin StatusRestrictedCommand
 */
trait IsStatusRestrictedCommand
{
    /**
     * @return StatusType[]
     */
    abstract protected function allowedStatusTypes(): array;

    /**
     * @throws InvalidStatusException
     */
    public function checkAllowedBy(Status $status): void
    {
        if (!in_array($status->type, $this->allowedStatusTypes(), strict: true)) {
            throw new InvalidStatusException($status->type, ...$this->allowedStatusTypes());
        }
    }
}
