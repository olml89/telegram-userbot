<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application;

use DateTimeInterface;
use olml89\TelegramUserbot\Backend\File\Domain\Audio;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\Image;
use olml89\TelegramUserbot\Backend\File\Domain\Thumbnail\ThumbnailDisplayer;
use olml89\TelegramUserbot\Backend\File\Domain\Video;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\IsResult;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\Result;

readonly class FileResult implements Result
{
    use IsResult;

    public string $publicId;
    public string $fileName;
    public string $originalName;
    public string $mimeType;
    public int $bytes;
    public bool $hasThumbnail;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(File $file)
    {
        $this->publicId = $file->publicId()->toRfc4122();
        $this->fileName = $file->fileName()->value;
        $this->originalName = $file->originalName()->value;
        $this->mimeType = $file->mimeType()->value;
        $this->bytes = $file->bytes()->value;
        $this->hasThumbnail = $file instanceof ThumbnailDisplayer;
        $this->createdAt = $file->createdAt()->format(DateTimeInterface::RFC3339);
        $this->updatedAt = $file->updatedAt()->format(DateTimeInterface::RFC3339);
    }

    public static function file(File $file): self
    {
        return match (true) {
            $file instanceof Image => new ImageResult($file),
            $file instanceof Audio => new AudioResult($file),
            $file instanceof Video => new VideoResult($file),
            default => new FileResult($file),
        };
    }
}
