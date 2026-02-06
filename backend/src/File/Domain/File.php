<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\File\Domain\Upload\Upload;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumed;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumptionException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\IsEntity;
use Symfony\Component\Uid\Uuid;

final class File implements Entity
{
    use IsEntity;

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
    public static function attach(Upload $upload, string $destinationDirectory): self
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

        return $file->attached($upload);
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

    public function attached(Upload $upload): self
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
