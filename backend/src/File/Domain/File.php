<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\Upload;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumed;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumptionException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\IsEntity;
use Symfony\Component\Uid\Uuid;

final class File implements Entity
{
    use IsEntity;

    private ?Content $content = null;

    public function __construct(
        protected readonly Uuid $publicId,
        private readonly string $name,
        private readonly string $originalName,
        private readonly string $mimeType,
        private readonly int $bytes,
    ) {
    }

    /**
     * @throws UploadConsumptionException
     */
    public static function fromUpload(Upload $upload, string $destinationDirectory): self
    {
        $publicId = Uuid::v4();

        $file = new self(
            publicId: $publicId,
            name: sprintf(
                '%s.%s',
                $publicId->toRfc4122(),
                $upload->extension(),
            ),
            originalName: $upload->originalName(),
            mimeType: $upload->mimeType(),
            bytes: $upload->bytes(),
        );

        $upload->move($destinationDirectory, $file);

        return $file->uploadConsumed($upload);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function originalName(): string
    {
        return $this->originalName;
    }

    public function mimeType(): string
    {
        return $this->mimeType;
    }

    public function bytes(): int
    {
        return $this->bytes;
    }

    public function isAttached(): bool
    {
        return !is_null($this->content);
    }

    /**
     * @throws FileAlreadyAttachedException
     */
    public function attach(Content $content): self
    {
        if ($this->isAttached()) {
            throw new FileAlreadyAttachedException($this);
        }

        $this->content = $content;

        return $this;
    }

    public function content(): ?Content
    {
        return $this->content;
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
