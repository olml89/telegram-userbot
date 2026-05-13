<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\File;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileName;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFile;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFileNotReadableException;
use olml89\TelegramUserbot\Backend\File\Domain\UnattachedFile;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\Upload;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumptionException;
use Symfony\Component\Filesystem\Filesystem;

final readonly class SymfonyFileManager implements FileManager
{
    public function __construct(
        private Filesystem $filesystem,
        private string $contentDirectory,
    ) {}

    /**
     * @throws UploadConsumptionException
     */
    public function consume(UnattachedFile $unattachedFile, Upload $upload): void
    {
        $upload->move($this->contentDirectory, $unattachedFile);
        $unattachedFile->uploadConsumed($upload);
    }

    /**
     * @throws StorageFileNotReadableException
     */
    public function storageFile(File|FileName $subject): StorageFile
    {
        $path = $subject->filePath($this->contentDirectory);

        return new StorageFile($path)->assertExists();
    }

    public function remove(File|StorageFile $file): void
    {
        $storageFile = $file instanceof StorageFile ? $file : $this->storageFile($file);

        $this->filesystem->remove($storageFile->getPathname());
    }
}
