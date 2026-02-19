<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\FileMetadataStrippers;

use Imagick;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\FileMetadataStrippingException;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\ImageMetadataStripper;
use olml89\TelegramUserbot\Backend\File\Domain\Image;
use Throwable;

final readonly class ImagickImageMetadataStripper implements ImageMetadataStripper
{
    public function __construct(
        private FileManager $fileManager,
    ) {}

    /**
     * @throws FileMetadataStrippingException
     */
    public function strip(Image $image): true
    {
        try {
            $storageFile = $this->fileManager->storageFile($image);
            $imagickFile = new Imagick($storageFile->getPathname());
            $imagickFile->stripImage();
            $imagickFile->writeImage($storageFile->getPathname());
            $imagickFile->clear();

            return true;
        } catch (Throwable $e) {
            throw new FileMetadataStrippingException($e);
        }
    }
}
