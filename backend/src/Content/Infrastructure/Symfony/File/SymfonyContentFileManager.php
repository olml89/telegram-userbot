<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\File;

use olml89\TelegramUserbot\Backend\Content\Domain\ContentFileManager;
use olml89\TelegramUserbot\Backend\Content\Domain\File;
use olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile\UploadedFile;
use olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile\UploadedFileException;
use Symfony\Component\Filesystem\Filesystem;

final readonly class SymfonyContentFileManager implements ContentFileManager
{
    public function __construct(
        private Filesystem $filesystem,
        private string $contentDirectory,
    ) {
    }

    /**
     * @throws UploadedFileException
     */
    public function save(UploadedFile $uploadedFile): File
    {
        return $uploadedFile->save($this->contentDirectory);
    }

    public function exists(File $file): bool
    {
        return $this->filesystem->exists($file->path($this->contentDirectory));
    }

    public function remove(File $file): void
    {
        $this->filesystem->remove($file->path($this->contentDirectory));
    }
}
