<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer;

use olml89\TelegramUserbot\Backend\File\Domain\Pdf;
use olml89\TelegramUserbot\Backend\File\Domain\UnattachedFile;

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
    public function specialize(UnattachedFile $unattachedFile): UnattachedFile
    {
        $mimeType = $unattachedFile->file()->mimeType();

        $specializedFile = match (true) {
            $mimeType->isImage() => $this->imageSpecializer->specialize($unattachedFile),
            $mimeType->isAudio() => $this->audioSpecializer->specialize($unattachedFile),
            $mimeType->isVideo() => $this->videoSpecializer->specialize($unattachedFile),
            $mimeType->isPdf() => new Pdf($unattachedFile),
            default => $unattachedFile->file(),
        };

        return $unattachedFile->replace($specializedFile);
    }
}
