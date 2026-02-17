<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\File\Domain\MimeType\MimeType;
use olml89\TelegramUserbot\Backend\File\Domain\OriginalName\OriginalName;
use olml89\TelegramUserbot\Backend\File\Domain\Size\Size;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\Upload;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumed;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\IsEntity;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name\Name;
use Symfony\Component\Uid\Uuid;

class File implements Entity
{
    use IsEntity;

    private ?Content $content = null;

    public function __construct(
        protected readonly Uuid $publicId,
        private readonly Name $name,
        private readonly OriginalName $originalName,
        private readonly MimeType $mimeType,
        private readonly Size $bytes,
    ) {}

    public function name(): Name
    {
        return $this->name;
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
        return sprintf('%s/%s', $directory, $this->name()->value);
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
}
