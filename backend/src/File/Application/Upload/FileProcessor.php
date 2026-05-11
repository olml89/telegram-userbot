<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Upload;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\FileMetadataStripper;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\FileMetadataStrippingException;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\FileSpecializationException;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\FileSpecializer;
use olml89\TelegramUserbot\Backend\File\Domain\UnattachedFile;

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
    public function process(UnattachedFile $unattachedFile): UnattachedFile
    {
        try {
            $unattachedFile = $this->fileSpecializer->specialize($unattachedFile);

            return $this->fileMetadataStripper->strip($unattachedFile);
        } catch (FileSpecializationException|FileMetadataStrippingException $e) {
            /**
             * Rollback: delete the StorageFile if there's an error while trying to process the File
             */
            $this->fileManager->remove($unattachedFile->file());

            throw $e;
        }
    }
}
