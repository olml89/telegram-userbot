<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Upload;

use olml89\TelegramUserbot\Backend\File\Application\AudioResult;
use olml89\TelegramUserbot\Backend\File\Application\FileResult;
use olml89\TelegramUserbot\Backend\File\Application\FileResultFactory;
use olml89\TelegramUserbot\Backend\File\Application\ImageResult;
use olml89\TelegramUserbot\Backend\File\Application\VideoResult;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\FileMetadataStrippingException;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\FileSpecializationException;
use olml89\TelegramUserbot\Backend\File\Domain\FileStorageException;
use olml89\TelegramUserbot\Backend\File\Domain\FileStorer;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumptionException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadNotFoundException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadReadingException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadRemovalException;
use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventDispatcher;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\UnsupportedResourceException;

final readonly class UploadFileCommandHandler
{
    public function __construct(
        private FileBuilder $fileBuilder,
        private FileStorer $fileStorer,
        private EventDispatcher $eventDispatcher,
        private FileResultFactory $fileResultFactory,
    ) {}

    /**
     * @throws UploadNotFoundException
     * @throws UploadReadingException
     * @throws UnsupportedResourceException
     * @throws ValidationException
     * @throws UploadConsumptionException
     * @throws UploadRemovalException
     * @throws FileMetadataStrippingException
     * @throws FileSpecializationException
     * @throws FileStorageException
     */
    public function handle(UploadFileCommand $command): FileResult|ImageResult|AudioResult|VideoResult
    {
        $file = $this->fileBuilder->build($command);
        $this->fileStorer->store($file);
        $this->eventDispatcher->dispatch(...$file->pullEvents());

        return $this->fileResultFactory->create($file);
    }
}
