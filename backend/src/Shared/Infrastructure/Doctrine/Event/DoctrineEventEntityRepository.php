<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Doctrine\Event;

use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventEntity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventEntityRepository;
use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Doctrine\DoctrineRepository;

/**
 * @extends DoctrineRepository<EventEntity>
 */
final class DoctrineEventEntityRepository extends DoctrineRepository implements EventEntityRepository
{
    protected static function entityClass(): string
    {
        return EventEntity::class;
    }

    public function store(EventEntity $eventEntity): void
    {
        $this->storeEntity($eventEntity);
    }
}
