<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event;

use DateTimeImmutable;

/**
 * @mixin Event
 */
trait IsEvent
{
    protected readonly DateTimeImmutable $occurredAt;

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
