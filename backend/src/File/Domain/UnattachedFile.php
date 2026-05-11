<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentFile\ContentFile;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\FileMetadataStripped;
use olml89\TelegramUserbot\Backend\File\Domain\Size\Size;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\Upload;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumed;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\EventSource\EventSource;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\EventSource\HasEvents;
use Symfony\Component\Uid\Uuid;

final class UnattachedFile implements EventSource
{
    use HasEvents;

    public function __construct(
        private readonly File $file,
    ) {}

    public function file(): File
    {
        return $this->file;
    }

    public function attach(Content $content): ContentFile
    {
        return new ContentFile(
            publicId: Uuid::v4(),
            content: $content,
            file: $this->file,
        );
    }

    public function replace(File $file): self
    {
        $newInstance = new self($file);

        foreach ($this->pullEvents() as $event) {
            $newInstance->record($event);
        }

        return $newInstance;
    }

    public function uploadConsumed(Upload $upload): self
    {
        return $this->record(new UploadConsumed($this, $upload));
    }

    public function strippedMetadata(Size $bytes): self
    {
        $oldSize = $this->file->bytes();
        $this->file->setBytes($bytes);

        return $this->record(new FileMetadataStripped($this, $oldSize));
    }

    public function stored(): self
    {
        return $this->record(new FileStored($this));
    }

    public function removed(): self
    {
        return $this->record(new FileRemoved($this));
    }
}
