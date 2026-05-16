<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\File;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileName;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFile;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFileNotReadableException;
use olml89\TelegramUserbot\Backend\File\Domain\Thumbnail\ThumbnailDisplayer;
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
        if ($file instanceof StorageFile) {
            $this->filesystem->remove($file->getPathname());

            return;
        }

        $path = $this->path($file);
        $this->filesystem->remove($path);

        /**
         * Remove also the thumbnail, if any
         */
        if ($file instanceof ThumbnailDisplayer) {
            $thumbnail = $file->thumbnail();
            $this->filesystem->remove($this->path($thumbnail));
        }

        /**
         * Remove also the file sharded directories, if empty
         */
        $directory = dirname($path);

        while ($directory !== $this->contentDirectory && $this->isEmpty($directory)) {
            $this->filesystem->remove($directory);
            $directory = dirname($directory);
        }
    }

    private function isEmpty(string $directory): bool
    {
        $scanDir = scandir($directory);

        return $scanDir !== false && count($scanDir) === 2;
    }
}
