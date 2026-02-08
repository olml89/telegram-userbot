<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use DateTimeImmutable;
use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\Event;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\IsEvent;

final readonly class FileAttached implements Event
{
    use IsEvent;

    public function __construct(
        public Content $content,
        public File $file,
        protected DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {
    }

    public function entity(): Entity
    {
        return $this->content;
    }

    public function jsonSerialize(): array
    {
        return [
            'fileId' => $this->file->id(),
        ];
    }
}
