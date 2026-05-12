<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Display;

use olml89\TelegramUserbot\Backend\File\Domain\FileFinder;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotFoundException;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFile;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFileNotReadableException;

final readonly class DisplayCommandHandler
{
    public function __construct(
        private FileFinder $fileFinder,
        private FileManager $fileManager,
    ) {}

    /**
     * @throws FileNotFoundException
     * @throws StorageFileNotReadableException
     */
    public function handle(DisplayCommand $command): StorageFile
    {
        $file = $this->fileFinder->find($command->publicId);

        return $this->fileManager->storageFile($file);
    }
}
