<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\File;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use Symfony\Component\Filesystem\Filesystem;

final readonly class SymfonyFileManager implements FileManager
{
    public function __construct(
        private Filesystem $filesystem,
        private string $contentDirectory
    ) {
    }

    public function exists(File $file): bool
    {
        return $this->filesystem->exists(sprintf('%s/%s', $this->contentDirectory, $file->name()));
    }

    public function remove(File $file): void
    {
        $this->filesystem->remove(sprintf('%s/%s', $this->contentDirectory, $file->name()));
    }
}
