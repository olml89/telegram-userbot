<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Entity;

use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\Event;

interface EventSource extends Entity
{
    /** @return Event[] */
    public function pullEvents(): array;
}
