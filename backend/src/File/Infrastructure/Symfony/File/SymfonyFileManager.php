<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\File;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileName;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFile;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFileNotReadableException;
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
    public function consume(File $file, Upload $upload): void
    {
        $upload->move($this->contentDirectory, $file);
        $file->uploadConsumed($upload);
    }

    public function exists(File $file): bool
    {
        return $this->filesystem->exists($file->path($this->contentDirectory));
    }

    /**
     * @throws StorageFileNotReadableException
     */
    public function mediaFile(File|FileName $subject): StorageFile
    {
        $path = $this->path($subject);

        return new StorageFile($path);
    }

    public function path(File|FileName $subject): string
    {
        return $subject->path($this->contentDirectory);
    }

    public function remove(File $file): void
    {
        $this->filesystem->remove($file->path($this->contentDirectory));
    }
}
