<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper;

use olml89\TelegramUserbot\Backend\File\Domain\File;

final readonly class FileMetadataStripper
{
    public function __construct(
        private ImageMetadataStripper $imageMetadataStripper,
    ) {}

    public function strip(File $file): void
    {
        match (true) {
            default => $this->imageMetadataStripper->strip($file),
        };
    }
}
