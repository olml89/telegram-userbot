<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper;

use olml89\TelegramUserbot\Backend\File\Domain\Audio;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\Image;
use olml89\TelegramUserbot\Backend\File\Domain\Pdf;
use olml89\TelegramUserbot\Backend\File\Domain\Size\Size;
use olml89\TelegramUserbot\Backend\File\Domain\Size\SizeException;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFileNotReadableException;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFileSizeException;
use olml89\TelegramUserbot\Backend\File\Domain\UnattachedFile;
use olml89\TelegramUserbot\Backend\File\Domain\Video;

final readonly class FileMetadataStripper
{
    public function __construct(
        private FileManager $fileManager,
        private ImageMetadataStripper $imageMetadataStripper,
        private AudioMetadataStripper $audioMetadataStripper,
        private VideoMetadataStripper $videoMetadataStripper,
        private PdfMetadataStripper $pdfMetadataStripper,
    ) {}

    /**
     * @throws FileMetadataStrippingException
     */
    public function strip(UnattachedFile $unattachedFile): UnattachedFile
    {
        $file = $unattachedFile->file();

        $hasStrippedMetadata = match (true) {
            $file instanceof Image => $this->imageMetadataStripper->strip($file),
            $file instanceof Audio => $this->audioMetadataStripper->strip($file),
            $file instanceof Video => $this->videoMetadataStripper->strip($file),
            $file instanceof Pdf => $this->pdfMetadataStripper->strip($file),
            default => false,
        };

        if (!$hasStrippedMetadata) {
            return $unattachedFile;
        }

        $newSize = $this->readNewSize($unattachedFile);

        return $unattachedFile->strippedMetadata($newSize);
    }

    /**
     * @throws FileMetadataStrippingException
     */
    private function readNewSize(UnattachedFile $unattachedFile): Size
    {
        try {
            $storageFile = $this->fileManager->storageFile($unattachedFile->file());
            $bytes = $storageFile->getSize();

            return new Size($bytes);
        } catch (StorageFileNotReadableException|StorageFileSizeException|SizeException $e) {
            throw new FileMetadataStrippingException($e);
        }
    }
}
