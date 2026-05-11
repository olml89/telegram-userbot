<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use DateTimeImmutable;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\Event;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\IsEvent;

final readonly class ContentRemoved implements Event
{
    use IsEvent;

    public function __construct(
        private Content $content,
        protected DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {}

    public function entity(): Content
    {
        return $this->content;
    }
}
