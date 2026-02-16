<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Upload;

use olml89\TelegramUserbot\Backend\File\Application\FileResult;
use olml89\TelegramUserbot\Backend\File\Domain\FileStorageException;
use olml89\TelegramUserbot\Backend\File\Domain\FileStorer;
use olml89\TelegramUserbot\Backend\File\Domain\MimeType\UnsupportedMimeTypeException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumptionException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadNotFoundException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadReadingException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadRemovalException;
use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventDispatcher;

final readonly class UploadFileCommandHandler
{
    public function __construct(
        private FileBuilder $fileBuilder,
        private FileStorer $fileStorer,
        private EventDispatcher $eventDispatcher,
    ) {
    }

    /**
     * @throws UploadNotFoundException
     * @throws UploadReadingException
     * @throws UnsupportedMimeTypeException
     * @throws ValidationException
     * @throws UploadConsumptionException
     * @throws UploadRemovalException
     * @throws FileStorageException
     */
    public function handle(UploadFileCommand $command): FileResult
    {
        $file = $this->fileBuilder->build($command);
        $this->fileStorer->store($file);
        $this->eventDispatcher->dispatch(...$file->events());

        return FileResult::file($file);
    }
}
