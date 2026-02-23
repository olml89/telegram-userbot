<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\Pdf;

final readonly class FileSpecializer
{
    public function __construct(
        private ImageSpecializer $imageSpecializer,
        private AudioSpecializer $audioSpecializer,
        private VideoSpecializer $videoSpecializer,
    ) {}

    /**
     * @throws FileSpecializationException
     */
    public function specialize(File $file): File
    {
        return match (true) {
            $file->mimeType()->isImage() => $this->imageSpecializer->specialize($file),
            $file->mimeType()->isAudio() => $this->audioSpecializer->specialize($file),
            $file->mimeType()->isVideo() => $this->videoSpecializer->specialize($file),
            $file->mimeType()->isPdf() => new Pdf($file),
            default => $file,
        };
    }
}
