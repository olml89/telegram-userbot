<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Remove;

use olml89\TelegramUserbot\Backend\File\Domain\FileAlreadyAttachedException;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotFoundException;
use olml89\TelegramUserbot\Backend\File\Domain\FileRemover;
use olml89\TelegramUserbot\Backend\File\Domain\FileStorageException;
use olml89\TelegramUserbot\Backend\File\Domain\UnattachedFileFinder;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventDispatcher;

final readonly class RemoveFileCommandHandler
{
    public function __construct(
        private UnattachedFileFinder $unattachedFileFinder,
        private FileRemover $fileRemover,
        private EventDispatcher $eventDispatcher,
    ) {}

    /**
     * @throws FileNotFoundException
     * @throws FileAlreadyAttachedException
     * @throws FileStorageException
     */
    public function handle(RemoveFileCommand $command): void
    {
        $unattachedFile = $this->unattachedFileFinder->find($command->publicId);
        $this->fileRemover->remove($unattachedFile);
        $this->eventDispatcher->dispatch(...$unattachedFile->pullEvents());
    }
}
