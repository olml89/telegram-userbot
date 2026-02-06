<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use Throwable;

final readonly class FileStorer
{
    public function __construct(
        private FileRepository $fileRepository,
        private FileManager $fileManager,
    ) {
    }

    /**
     * @throws FileStorageException
     */
    public function store(File $file): void
    {
        try {
            $this->fileRepository->store($file);
            $file->stored();
        } catch (Throwable $e) {
            /**
             * Rollback: delete File data if there's an error while trying to store File
             */
            $this->fileManager->remove($file);

            throw FileStorageException::store($file, $e);
        }
    }
}
