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

    public function path(FileName|File $subject): string
    {
        return $subject->filePath($this->contentDirectory);
    }

    /**
     * @throws StorageFileNotReadableException
     */
    public function storageFile(File|FileName $subject): StorageFile
    {
        return new StorageFile($this->path($subject))->assertExists();
    }

    public function remove(File|StorageFile $file): void
    {
        $path = $file instanceof File
            ? $file->filePath($this->contentDirectory)
            : $file->getPathname();

        $this->filesystem->remove($path);
    }
}
