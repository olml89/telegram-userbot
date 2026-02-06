<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use DateTimeImmutable;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\Event;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\IsEvent;

final readonly class FileRemoved implements Event
{
    use IsEvent;

    public function __construct(
        private File $file,
        protected DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {
    }

    public function entity(): File
    {
        return $this->file;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->file->name(),
        ];
    }
}
