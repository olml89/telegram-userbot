<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use Throwable;

final readonly class FileRemover
{
    public function __construct(
        private UnattachedFileRepository $fileRepository,
        private FileManager              $fileManager,
    ) {}

    /**
     * @throws FileStorageException
     */
    public function remove(UnattachedFile $unattachedFile): void
    {
        try {
            $this->fileRepository->remove($unattachedFile);
            $this->fileManager->remove($unattachedFile->file());
            $unattachedFile->removed();
        } catch (Throwable $e) {
            throw FileStorageException::remove($unattachedFile, $e);
        }
    }
}
