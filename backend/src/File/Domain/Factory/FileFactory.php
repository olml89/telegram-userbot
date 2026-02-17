<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Factory;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\MimeType\MimeType;
use olml89\TelegramUserbot\Backend\File\Domain\OriginalName\OriginalName;
use olml89\TelegramUserbot\Backend\File\Domain\Size\Size;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name\Name;
use Symfony\Component\Uid\Uuid;

final readonly class FileFactory
{
    public function create(Uuid $fileId, Name $name, OriginalName $originalName, MimeType $mimeType, Size $bytes): File
    {
        return match (true) {
            default => new File(
                publicId: $fileId,
                name: $name,
                originalName: $originalName,
                mimeType: $mimeType,
                bytes: $bytes,
            ),
        };
    }
}
