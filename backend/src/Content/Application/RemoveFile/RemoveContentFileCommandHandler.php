<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\RemoveFile;

use olml89\TelegramUserbot\Backend\Content\Domain\ContentFinder;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentNotFoundException;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentFile\ContentFileRemover;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotFoundException;
use olml89\TelegramUserbot\Backend\File\Domain\FileStorageException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventDispatcher;

final readonly class RemoveContentFileCommandHandler
{
    public function __construct(
        private ContentFinder $contentFinder,
        private ContentFileRemover $fileRemover,
        private EventDispatcher $eventDispatcher,
    ) {}

    /**0
     * @throws ContentNotFoundException
     * @throws FileNotFoundException
     * @throws FileStorageException
     */
    public function handle(RemoveContentFileCommand $command): void
    {
        $content = $this->contentFinder->find($command->contentId);
        $this->fileRemover->remove($content, $command->fileId);
        $this->eventDispatcher->dispatch(...$content->pullEvents());
    }
}
