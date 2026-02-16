<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event;

use DateTimeImmutable;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\IsEntity;
use Symfony\Component\Uid\Uuid;

final class EventEntity implements Entity
{
    use IsEntity;

    public function __construct(
        protected readonly Uuid $publicId,
        private readonly string $eventClass,
        private readonly string $entityClass,
        private readonly Uuid $entityId,
        /** @var array<string, mixed> */
        private readonly array $payload,
        private readonly DateTimeImmutable $occurredAt,
    ) {}

    public function eventClass(): string
    {
        return $this->eventClass;
    }

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

    public function occuredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
