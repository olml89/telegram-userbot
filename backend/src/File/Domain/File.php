<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\FileMetadataStripped;
use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileName;
use olml89\TelegramUserbot\Backend\File\Domain\MimeType\MimeType;
use olml89\TelegramUserbot\Backend\File\Domain\OriginalName\OriginalName;
use olml89\TelegramUserbot\Backend\File\Domain\Size\Size;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\Upload;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumed;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\EventSource\EventSource;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\EventSource\HasEvents;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\HasIdentity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Timestampable\HasTimestamps;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Timestampable\Timestampable;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Timestamps\Timestamps;
use Symfony\Component\Uid\Uuid;

class File implements EventSource, Timestampable
{
    use HasIdentity;
    use HasEvents;
    use HasTimestamps;

    protected ?Content $content = null;

    public function __construct(
        protected readonly Uuid $publicId,
        protected readonly FileName $fileName,
        protected readonly OriginalName $originalName,
        protected readonly MimeType $mimeType,
        protected readonly Size $bytes,
        protected readonly Timestamps $timestamps = new Timestamps(),
    ) {}

    final protected function copyEvents(File $file): static
    {
        foreach ($file->pullEvents() as $event) {
            $this->record($event);
        }

        return $this;
    }

    public function fileName(): FileName
    {
        return $this->fileName;
    }

    public function originalName(): OriginalName
    {
        return $this->originalName;
    }

    public function mimeType(): MimeType
    {
        return $this->mimeType;
    }

    public function bytes(): Size
    {
        return $this->bytes;
    }

    public function content(): ?Content
    {
        return $this->content;
    }

    public function path(string $directory): string
    {
        return $this->fileName()->path($directory);
    }

    public function assertNotAttached(): self
    {
        if (!is_null($this->content)) {
            throw new FileAlreadyAttachedException($this);
        }

        return $this;
    }

    /**
     * @throws FileAlreadyAttachedException
     */
    public function attach(Content $content): self
    {
        $this->assertNotAttached();
        $this->content = $content;

        return $this;
    }

    public function uploadConsumed(Upload $upload): self
    {
        return $this->record(new UploadConsumed($this, $upload));
    }

    public function stored(): self
    {
        return $this->record(new FileStored($this));
    }

    public function removed(): self
    {
        return $this->record(new FileRemoved($this));
    }

    public function strippedMetadata(Size $newSize): static
    {
        $strippedMetadata = new self(
            publicId: $this->publicId,
            fileName: $this->fileName,
            originalName: $this->originalName,
            mimeType: $this->mimeType,
            bytes: $newSize,
        );

        return $strippedMetadata
            ->copyEvents($this)
            ->record(new FileMetadataStripped($this, $newSize));
    }
}
