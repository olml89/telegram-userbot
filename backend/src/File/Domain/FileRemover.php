<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use Throwable;

final readonly class FileRemover
{
    public function __construct(
        private FileRepository $fileRepository,
        private FileManager $fileManager,
    ) {
    }

    /**
     * @throws FileStorageException
     */
    public function remove(File $file): void
    {
        try {
            $snapshot = clone $file;
            $this->fileRepository->remove($file);
            $this->fileManager->remove($file);
            $file->record(new FileRemoved($snapshot));
        } catch (Throwable $e) {
            throw FileStorageException::remove($file, $e);
        }
    }
}
