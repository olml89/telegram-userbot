<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer;

use olml89\TelegramUserbot\Backend\File\Domain\File;

final readonly class FileSpecializer
{
    public function __construct(
        private ImageSpecializer $imageSpecializer,
        private AudioSpecializer $audioSpecializer,
    ) {}

    /**
     * @throws FileSpecializationException
     */
    public function specialize(File $file): File
    {
        return match (true) {
            $file->mimeType()->isImage() => $this->imageSpecializer->specialize($file),
            $file->mimeType()->isAudio() => $this->audioSpecializer->specialize($file),
            default => $file,
        };
    }
}
