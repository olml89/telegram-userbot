<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application;

use DateTimeInterface;
use olml89\TelegramUserbot\Backend\Category\Application\CategoryResult;
use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Content\Domain\Language\Language;
use olml89\TelegramUserbot\Backend\Content\Domain\Mode\Mode;
use olml89\TelegramUserbot\Backend\Content\Domain\Status\Status;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\IsResult;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\Result;
use olml89\TelegramUserbot\Backend\Tag\Application\TagResult;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;

final readonly class ContentResult implements Result
{
    use IsResult;

    public function __construct(
        public string $publicId,
        public string $title,
        public string $description,
        public float $price,
        public int $intensity,
        public int $sales,
        public Mode $mode,
        public Status $status,
        public Language $language,
        public CategoryResult $category,

        /** @var TagResult[] */
        public array $tags,

        public ContentFileContainer $files,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function content(Content $content): self
    {
        /** @var TagResult[] $tagResults */
        $tagResults = $content
            ->tags()
            ->map(fn(Tag $tag): TagResult => TagResult::tag($tag))
            ->values()
            ->toArray();

        return new self(
            publicId: $content->publicId()->toRfc4122(),
            title: $content->title()->value,
            description: $content->description()->value,
            price: $content->price()->value,
            intensity: $content->intensity()->value,
            sales: $content->sales(),
            mode: $content->mode(),
            status: $content->status(),
            language: $content->language(),
            category: CategoryResult::category($content->category()),
            tags: $tagResults,
            files: ContentFileContainer::files($content->contentFiles()),
            createdAt: $content->createdAt()->format(DateTimeInterface::RFC3339),
            updatedAt: $content->updatedAt()->format(DateTimeInterface::RFC3339),
        );
    }
}
