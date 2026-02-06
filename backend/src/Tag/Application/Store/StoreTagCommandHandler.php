<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Application\Store;

use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventDispatcher;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagStorer;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagFinder;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagNotFoundException;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagStorageException;
use Symfony\Component\Uid\Uuid;

final readonly class StoreTagCommandHandler
{
    public function __construct(
        private TagFinder $tagFinder,
        private TagStorer $tagStorer,
        private EventDispatcher $eventDispatcher,
    ) {
    }

    /**
     * @throws TagStorageException
     */
    public function handle(StoreTagCommand $command): StoreTagResult
    {
        try {
            $tag = $this->tagFinder->findByName($command->name);

            return StoreTagResult::found($tag);
        } catch (TagNotFoundException) {
            $tag = new Tag(
                publicId: Uuid::v4(),
                name: $command->name,
            );
            $this->tagStorer->store($tag);
            $this->eventDispatcher->dispatch(...$tag->events());

            return StoreTagResult::created($tag);
        }
    }
}
