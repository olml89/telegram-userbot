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
    public function store(File $file): File
    {
        try {
            $this->fileRepository->store($file);

            return $file->record(new FileStored($file));
        } catch (Throwable $e) {
            /**
             * Rollback: delete File data if there's an error while trying to store File
             */
            $this->fileManager->remove($file);

            throw FileStorageException::store($file, $e);
        }
    }
}
