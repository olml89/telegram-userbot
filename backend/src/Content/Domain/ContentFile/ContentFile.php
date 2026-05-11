<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\ContentFile;

use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\UnattachedFile;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\EventSource\EventSource;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\EventSource\HasEvents;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\HasIdentity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Timestampable\HasTimestamps;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Timestampable\Timestampable;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Timestamps\Timestamps;
use Symfony\Component\Uid\Uuid;

final class ContentFile implements Entity, EventSource, Timestampable
{
    use HasIdentity;
    use HasEvents;
    use HasTimestamps;

    public function __construct(
        protected readonly Uuid $publicId,
        private readonly Content $content,
        private readonly File $file,
        protected readonly Timestamps $timestamps = new Timestamps(),
    ) {}

    public function content(): Content
    {
        return $this->content;
    }

    public function file(): File
    {
        return $this->file;
    }

    public function attached(): self
    {
        return $this->record(new ContentFileAttached($this));
    }

    public function removed(): self
    {
        return $this->record(new ContentFileRemoved($this));
    }

    public function detach(): UnattachedFile
    {
        return new UnattachedFile($this->file);
    }
}
