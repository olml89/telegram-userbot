<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\Remove;

use olml89\TelegramUserbot\Backend\Content\Domain\ContentFinder;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentNotFoundException;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentRemover;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentStorageException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventDispatcher;

final readonly class RemoveContentCommandHandler
{
    public function __construct(
        private ContentFinder $contentFinder,
        private ContentRemover $contentRemover,
        private EventDispatcher $eventDispatcher,
    ) {}

    /**
     * @throws ContentNotFoundException
     * @throws ContentStorageException
     */
    public function handle(RemoveContentCommand $command): void
    {
        $content = $this->contentFinder->find($command->publicId);
        $this->contentRemover->remove($content);
        $this->eventDispatcher->dispatch(...$content->pullEvents());
    }
}
