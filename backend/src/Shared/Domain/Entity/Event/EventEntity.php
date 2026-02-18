<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event;

use DateTimeImmutable;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\HasIdentity;
use Symfony\Component\Uid\Uuid;

final class EventEntity implements Entity
{
    use HasIdentity;

    public function __construct(
        protected readonly Uuid $publicId,
        /** @var class-string<Event> */
        private readonly string $eventClass,
        /** @var class-string<Entity> */
        private readonly string $entityClass,
        private readonly Uuid $entityId,
        /** @var array<string, mixed> */
        private readonly array $payload,
        private readonly DateTimeImmutable $occurredAt,
    ) {}

    /**
     * @return class-string<Event>
     */
    public function eventClass(): string
    {
        return $this->eventClass;
    }

    /**
     * @return class-string<Entity>
     */
    public function entityClass(): string
    {
        return $this->entityClass;
    }

    public function entityId(): Uuid
    {
        return $this->entityId;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->payload;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
