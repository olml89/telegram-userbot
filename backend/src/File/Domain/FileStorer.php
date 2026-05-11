<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use Throwable;

final readonly class FileStorer
{
    public function __construct(
        private UnattachedFileRepository $fileRepository,
        private FileManager              $fileManager,
    ) {}

    /**
     * @throws FileStorageException
     */
    public function store(UnattachedFile $unattachedFile): void
    {
        try {
            $this->fileRepository->store($unattachedFile);
            $unattachedFile->stored();
        } catch (Throwable $e) {
            /**
             * Rollback: delete File data if there's an error while trying to store File
             */
            $this->fileManager->remove($unattachedFile->file());

            throw FileStorageException::store($unattachedFile, $e);
        }
    }
}
