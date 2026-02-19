<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application;

use olml89\TelegramUserbot\Backend\File\Domain\Image;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\IsResult;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\Result;

final readonly class ImageResult implements Result
{
    use IsResult;

    public function __construct(
        public string $publicId,
        public string $fileName,
        public string $originalName,
        public string $mimeType,
        public int $bytes,
        public int $width,
        public int $height,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function image(Image $image): self
    {
        $fileResult = FileResult::file($image);

        return new self(
            publicId: $fileResult->publicId,
            fileName: $fileResult->fileName,
            originalName: $fileResult->originalName,
            mimeType: $fileResult->mimeType,
            bytes: $fileResult->bytes,
            width: $image->resolution()->width,
            height: $image->resolution()->height,
            createdAt: $fileResult->createdAt,
            updatedAt: $fileResult->updatedAt,
        );
    }
}
