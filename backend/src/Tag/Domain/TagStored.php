<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Domain;

use DateTimeImmutable;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\Event;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\IsEvent;

final readonly class TagStored implements Event
{
    use IsEvent;

    public function __construct(
        private Tag $tag,
        protected DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {
    }

    public function entity(): Tag
    {
        return $this->tag;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [];
    }
}
