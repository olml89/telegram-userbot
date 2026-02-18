<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\EventSubscribers;

use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\Event;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventEntity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventEntityRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Uid\Uuid;

#[AsEventListener]
final readonly class EntityEventSubscriber
{
    public function __construct(
        private EventEntityRepository $eventEntityRepository,
    ) {}

    public function __invoke(Event $event): void
    {
        $eventEntity = new EventEntity(
            publicId: Uuid::v4(),
            eventClass: $event::class,
            entityClass: $event->entity()::class,
            entityId: $event->entity()->publicId(),
            payload: $event->jsonSerialize(),
            occurredAt: $event->occurredAt(),
        );

        $this->eventEntityRepository->store($eventEntity);
    }
}
