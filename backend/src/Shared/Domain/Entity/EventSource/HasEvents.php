<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Entity\EventSource;

use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\Event;

/**
 * @mixin EventSource
 */
trait HasEvents
{
    /**
     * @var Event[]
     */
    private array $events = [];

    final protected function record(Event $event): static
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * @return Event[]
     */
    final public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
