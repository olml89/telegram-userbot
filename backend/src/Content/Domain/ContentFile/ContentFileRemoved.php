<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\ContentFile;

use DateTimeImmutable;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\Event;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\IsEvent;

final readonly class ContentFileRemoved implements Event
{
    use IsEvent;

    public function __construct(
        public ContentFile $contentFile,
        protected DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {}

    public function entity(): Entity
    {
        return $this->contentFile;
    }
}
