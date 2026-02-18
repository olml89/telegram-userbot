<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\File;

use LogicException;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileName;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotReadableException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\Upload;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumptionException;
use RuntimeException;
use SplFileObject;
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
     * @throws FileNotReadableException
     */
    public function mediaFile(File|FileName $subject): SplFileObject
    {
        $path = $this->path($subject);

        try {
            return new SplFileObject($path);
        } catch (LogicException|RuntimeException $e) {
            throw new FileNotReadableException($path, $e);
        }
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
