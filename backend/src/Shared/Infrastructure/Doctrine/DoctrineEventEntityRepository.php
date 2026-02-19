<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Doctrine;

use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\EventEntity\EventEntity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\EventEntity\EventEntityRepository;

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
