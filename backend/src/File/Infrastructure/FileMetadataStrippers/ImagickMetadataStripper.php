<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\FileMetadataStrippers;

use Imagick;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\FileMetadataStrippingException;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\ImageMetadataStripper;
use Throwable;

final readonly class ImagickMetadataStripper implements ImageMetadataStripper
{
    public function __construct(
        private FileManager $fileManager,
    ) {}

    /**
     * @throws FileMetadataStrippingException
     */
    public function strip(File $file): void
    {
        try {
            $imageFile = $this->fileManager->mediaFile($file);
            $imagickFile = new Imagick($imageFile->getPathname());
            $imagickFile->stripImage();
            $imagickFile->writeImage($imageFile->getPathname());
            $imagickFile->clear();
        } catch (Throwable $e) {
            throw new FileMetadataStrippingException($e);
        }
    }
}
