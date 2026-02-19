<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Upload;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\FileMetadataStripper;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\FileMetadataStrippingException;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\FileSpecializationException;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\FileSpecializer;

final readonly class FileProcessor
{
    public function __construct(
        private FileManager $fileManager,
        private FileMetadataStripper $fileMetadataStripper,
        private FileSpecializer $fileSpecializer,
    ) {}

    /**
     * @throws FileSpecializationException
     * @throws FileMetadataStrippingException
     */
    public function process(File $file): File
    {
        try {
            $file = $this->fileSpecializer->specialize($file);

            return $this->fileMetadataStripper->strip($file);
        } catch (FileSpecializationException|FileMetadataStrippingException $e) {
            /**
             * Rollback: delete the StorageFile if there's an error while trying to process the File
             */
            $this->fileManager->remove($file);

            throw $e;
        }
    }
}
