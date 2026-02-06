<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\Store;

use olml89\TelegramUserbot\Backend\Category\Domain\CategoryFinder;
use olml89\TelegramUserbot\Backend\Category\Domain\CategoryNotFoundException;
use olml89\TelegramUserbot\Backend\Content\Application\ContentResult;
use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentStorageException;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentStorer;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileFinder;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotFoundException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\EventDispatcher;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagFinder;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagNotFoundException;
use Symfony\Component\Uid\Uuid;

final readonly class StoreContentCommandHandler
{
    public function __construct(
        private CategoryFinder $categoryFinder,
        private TagFinder $tagFinder,
        private FileFinder $fileFinder,
        private ContentStorer $contentStorer,
        private EventDispatcher $eventDispatcher,
    ) {
    }

    /**
     * @throws CategoryNotFoundException
     * @throws TagNotFoundException
     * @throws FileNotFoundException
     * @throws ContentStorageException
     */
    public function handle(StoreContentCommand $command): ContentResult
    {
        $category = $this->categoryFinder->find($command->categoryId);

        $tags = array_map(
            fn (Uuid $tagId): Tag => $this->tagFinder->find($tagId),
            $command->tagIds,
        );

        $files = array_map(
            fn (Uuid $fileId): File => $this->fileFinder->find($fileId),
            $command->fileIds,
        );

        $content = new Content(
            publicId: Uuid::v4(),
            title: $command->title,
            description: $command->description,
            intensity: $command->intensity,
            price: $command->price,
            language: $command->language,
            mode: $command->mode,
            status: $command->status,
            category: $category,
            tags: $tags,
            files: $files,
        );

        $this->contentStorer->store($content);
        $this->eventDispatcher->dispatch(...$content->events());

        return ContentResult::content($content);
    }
}
