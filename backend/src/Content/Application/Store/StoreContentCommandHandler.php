<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\Store;

use olml89\TelegramUserbot\Backend\Content\Application\ContentResult;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentStorageException;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentStorer;
use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventDispatcher;

final readonly class StoreContentCommandHandler
{
    public function __construct(
        private ContentBuilder $contentBuilder,
        private ContentStorer $contentStorer,
        private EventDispatcher $eventDispatcher,
    ) {
    }

    /**
     * @throws ValidationException
     * @throws ContentStorageException
     */
    public function handle(StoreContentCommand $command): ContentResult
    {
        $content = $this->contentBuilder->build($command);
        $this->contentStorer->store($content);
        $this->eventDispatcher->dispatch(...$content->events());

        return ContentResult::content($content);
    }
}
