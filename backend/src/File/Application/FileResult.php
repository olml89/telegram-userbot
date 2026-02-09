<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\IsResult;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\Result;

final readonly class FileResult implements Result
{
    use IsResult;

    public function __construct(
        public string $publicId,
        public string $name,
        public string $originalName,
        public string $mimeType,
        public int $bytes,
    ) {
    }

    public static function file(File $file): self
    {
        return new self(
            publicId: $file->publicId()->toRfc4122(),
            name: $file->name(),
            originalName: $file->originalName(),
            mimeType: $file->mimeType(),
            bytes: $file->bytes(),
        );
    }
}
