<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\File;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\Upload;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumptionException;
use Symfony\Component\Filesystem\Filesystem;

final readonly class SymfonyFileManager implements FileManager
{
    public function __construct(
        private Filesystem $filesystem,
        private string $contentDirectory
    ) {
    }

    private function path(File $file): string
    {
        return sprintf('%s/%s', $this->contentDirectory, $file->name()->value);
    }

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
        return $this->filesystem->exists($this->path($file));
    }

    public function remove(File $file): void
    {
        $this->filesystem->remove($this->path($file));
    }
}
