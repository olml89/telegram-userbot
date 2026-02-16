<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\Store;

use olml89\TelegramUserbot\Backend\Category\Domain\Category;
use olml89\TelegramUserbot\Backend\Category\Domain\CategoryFinder;
use olml89\TelegramUserbot\Backend\Category\Domain\CategoryNotFoundException;
use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentFinder;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentNotFoundException;
use olml89\TelegramUserbot\Backend\Content\Domain\Description;
use olml89\TelegramUserbot\Backend\Content\Domain\File\FileCollection;
use olml89\TelegramUserbot\Backend\Content\Domain\File\FileCollectionCountException;
use olml89\TelegramUserbot\Backend\Content\Domain\Price;
use olml89\TelegramUserbot\Backend\Content\Domain\Tag\TagCollection;
use olml89\TelegramUserbot\Backend\Content\Domain\Tag\TagCollectionCountException;
use olml89\TelegramUserbot\Backend\Content\Domain\Title;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileAlreadyAttachedException;
use olml89\TelegramUserbot\Backend\File\Domain\FileFinder;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotFoundException;
use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\OutOfRangeException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\StringLengthException;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Percentage\Percentage;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagFinder;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagNotFoundException;
use Symfony\Component\Uid\Uuid;

final readonly class ContentBuilder
{
    public function __construct(
        private ContentFinder $contentFinder,
        private CategoryFinder $categoryFinder,
        private TagFinder $tagFinder,
        private FileFinder $fileFinder,
    ) {}

    /**
     * @throws ValidationException
     */
    public function build(StoreContentCommand $command): Content
    {
        $validationException = new ValidationException();
        $title = $this->buildTitle($validationException, $command);
        $description = $this->buildDescription($validationException, $command);
        $intensity = $this->buildIntensity($validationException, $command);
        $price = $this->buildPrice($validationException, $command);
        $category = $this->buildCategory($validationException, $command);
        $tags = $this->buildTags($validationException, $command);
        $files = $this->buildFiles($validationException, $command);

        if ($validationException->hasErrors()) {
            throw $validationException;
        }

        /**
         * @var Title $title
         * @var Description $description
         * @var Percentage $intensity
         * @var Price $price
         * @var Category $category
         * @var TagCollection $tags
         * @var FileCollection $files
         */
        return new Content(
            publicId: Uuid::v4(),
            title: $title,
            description: $description,
            intensity: $intensity,
            price: $price,
            language: $command->language,
            mode: $command->mode,
            status: $command->status,
            category: $category,
            tags: $tags,
            files: $files,
        );
    }

    private function buildTitle(ValidationException $validationException, StoreContentCommand $command): ?Title
    {
        try {
            $title = new Title($command->title);

            try {
                $this->contentFinder->findByTitle($command->title);
                $validationException->addError('title', 'Title already exists.');

                return null;
            } catch (ContentNotFoundException) {
                return $title;
            }
        } catch (StringLengthException $e) {
            $validationException->addError('title', $e->getMessage());

            return null;
        }
    }

    private function buildDescription(ValidationException $validationException, StoreContentCommand $command): ?Description
    {
        try {
            return new Description($command->description);
        } catch (StringLengthException $e) {
            $validationException->addError('description', $e->getMessage());

            return null;
        }
    }

    private function buildIntensity(ValidationException $validationException, StoreContentCommand $command): ?Percentage
    {
        try {
            return new Percentage($command->intensity);
        } catch (OutOfRangeException $e) {
            $validationException->addError('intensity', $e->getMessage());

            return null;
        }
    }

    private function buildPrice(ValidationException $validationException, StoreContentCommand $command): ?Price
    {
        try {
            return new Price($command->price);
        } catch (OutOfRangeException $e) {
            $validationException->addError('price', $e->getMessage());

            return null;
        }
    }

    private function buildCategory(ValidationException $validationException, StoreContentCommand $command): ?Category
    {
        try {
            return $this->categoryFinder->find($command->categoryId);
        } catch (CategoryNotFoundException $e) {
            $validationException->addError('categoryId', $e->getMessage());

            return null;
        }
    }

    private function buildTags(ValidationException $validationException, StoreContentCommand $command): ?TagCollection
    {
        try {
            $tags = [];

            foreach ($command->tagIds as $tagId) {
                if (!is_null($tag = $this->buildTag($validationException, $tagId))) {
                    $tags[] = $tag;
                }
            }

            return new TagCollection(...$tags);
        } catch (TagCollectionCountException $e) {
            $validationException->addError('tagIds', $e->getMessage());

            return null;
        }
    }

    private function buildTag(ValidationException $validationException, Uuid $tagId): ?Tag
    {
        try {
            return $this->tagFinder->find($tagId);
        } catch (TagNotFoundException $e) {
            $validationException->addError('tagIds', $e->getMessage());

            return null;
        }
    }

    private function buildFiles(ValidationException $validationException, StoreContentCommand $command): ?FileCollection
    {
        try {
            $files = [];

            foreach ($command->fileIds as $fileId) {
                if (!is_null($file = $this->buildFile($validationException, $fileId))) {
                    $files[] = $file;
                }
            }

            return new FileCollection(...$files);
        } catch (FileCollectionCountException $e) {
            $validationException->addError('fileIds', $e->getMessage());

            return null;
        }
    }

    private function buildFile(ValidationException $validationException, Uuid $fileId): ?File
    {
        try {
            return $this->fileFinder->find($fileId)->assertNotAttached();
        } catch (FileNotFoundException|FileAlreadyAttachedException $e) {
            $validationException->addError('fileIds', $e->getMessage());

            return null;
        }
    }
}
