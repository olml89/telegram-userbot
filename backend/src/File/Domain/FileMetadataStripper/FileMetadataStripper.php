<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\Size\Size;
use olml89\TelegramUserbot\Backend\File\Domain\Size\SizeException;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFileNotReadableException;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFileSizeException;

final readonly class FileMetadataStripper
{
    public function __construct(
        private FileManager $fileManager,
        private ImageMetadataStripper $imageMetadataStripper,
    ) {}

    /**
     * @throws FileMetadataStrippingException
     */
    public function strip(File $file): File
    {
        match (true) {
            default => $this->imageMetadataStripper->strip($file),
        };

        $newSize = $this->readNewSize($file);

        return $file->strippedMetadata($newSize);
    }

    /**
     * @throws FileMetadataStrippingException
     */
    private function readNewSize(File $file): Size
    {
        try {
            $storageFile = $this->fileManager->storageFile($file);
            $bytes = $storageFile->getSize();

            return new Size($bytes);
        } catch (StorageFileNotReadableException|StorageFileSizeException|SizeException $e) {
            throw new FileMetadataStrippingException($e);
        }
    }
}
