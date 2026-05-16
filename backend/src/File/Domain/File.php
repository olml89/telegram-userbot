<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileName;
use olml89\TelegramUserbot\Backend\File\Domain\MimeType\MimeType;
use olml89\TelegramUserbot\Backend\File\Domain\OriginalName\OriginalName;
use olml89\TelegramUserbot\Backend\File\Domain\Size\Size;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\HasIdentity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Timestampable\HasTimestamps;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Timestampable\Timestampable;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Timestamps\Timestamps;
use Symfony\Component\Uid\Uuid;

class File implements Entity, Timestampable
{
    use HasIdentity;
    use HasTimestamps;

    public function __construct(
        protected readonly Uuid $publicId,
        protected readonly FileName $fileName,
        protected readonly OriginalName $originalName,
        protected readonly MimeType $mimeType,
        protected Size $bytes,
        protected readonly Timestamps $timestamps = new Timestamps(),
    ) {}

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

    public function setBytes(Size $bytes): void
    {
        $this->bytes = $bytes;
    }

    public function filePath(string $baseDirectory): string
    {
        return $this->fileName()->filePath($baseDirectory);
    }
}
