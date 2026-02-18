<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Remove;

use olml89\TelegramUserbot\Backend\File\Domain\FileFinder;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotFoundException;
use olml89\TelegramUserbot\Backend\File\Domain\FileRemover;
use olml89\TelegramUserbot\Backend\File\Domain\FileStorageException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventDispatcher;

final readonly class RemoveFileCommandHandler
{
    public function __construct(
        private FileFinder $fileFinder,
        private FileRemover $fileRemover,
        private EventDispatcher $eventDispatcher,
    ) {}

    /**
     * @throws FileNotFoundException
     * @throws FileStorageException
     */
    public function handle(RemoveFileCommand $command): void
    {
        $file = $this->fileFinder->find($command->publicId);
        $this->fileRemover->remove($file);
        $this->eventDispatcher->dispatch(...$file->pullEvents());
    }
}
