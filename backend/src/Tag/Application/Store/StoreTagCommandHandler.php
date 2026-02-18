<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Application\Store;

use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventDispatcher;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagRepository;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagStorageException;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagStorer;

final readonly class StoreTagCommandHandler
{
    public function __construct(
        private TagBuilder $tagBuilder,
        private TagRepository $tagRepository,
        private TagStorer $tagStorer,
        private EventDispatcher $eventDispatcher,
    ) {}

    /**
     * @throws ValidationException
     * @throws TagStorageException
     */
    public function handle(StoreTagCommand $command): StoreTagResult
    {
        $tag = $this->tagBuilder->build($command);

        if (!is_null($sameNameTag = $this->tagRepository->getByName($tag->name()))) {
            return StoreTagResult::found($sameNameTag);
        }

        $this->tagStorer->store($tag);
        $this->eventDispatcher->dispatch(...$tag->pullEvents());

        return StoreTagResult::created($tag);
    }
}
