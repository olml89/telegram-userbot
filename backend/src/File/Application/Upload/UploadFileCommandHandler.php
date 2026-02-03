<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Upload;

use olml89\TelegramUserbot\Backend\File\Domain\FileStorer;
use olml89\TelegramUserbot\Backend\File\Domain\FileStorageException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumer;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumptionException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadFinder;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadNotFoundException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventDispatcher;

final readonly class UploadFileCommandHandler
{
    public function __construct(
        private UploadFinder $uploadFinder,
        private UploadConsumer $uploadConsumer,
        private FileStorer $fileSaver,
        private EventDispatcher $eventDispatcher,
    ) {
    }

    /**
     * @throws UploadNotFoundException
     * @throws UploadConsumptionException
     * @throws FileStorageException
     */
    public function handle(UploadFileCommand $command): UploadFileResult
    {
        $upload = $this->uploadFinder->find($command->uploadId);
        $file = $this->uploadConsumer->consume($upload);
        $this->fileSaver->store($file);
        $this->eventDispatcher->dispatch(...$file->events());

        return UploadFileResult::file($file);
    }
}
