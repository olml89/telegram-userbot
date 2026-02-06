<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\EventDispatcher;

use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\Event;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class EntityEventDispatcher implements EventDispatcher
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function dispatch(Event ...$events): void
    {
        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event, eventName: Event::class);
        }
    }
}
