<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application;

use olml89\TelegramUserbot\Backend\Category\Application\CategoryResult;
use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Content\Domain\Mode;
use olml89\TelegramUserbot\Backend\Content\Domain\Status;
use olml89\TelegramUserbot\Backend\File\Application\FileResult;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\Tag\Application\TagResult;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;

final readonly class ContentResult
{
    public function __construct(
        public string $title,
        public string $description,
        public float $price,
        public int $sales,
        public Mode $mode,
        public Status $status,
        public CategoryResult $category,

        /** @var TagResult[] */
        public array $tags,

        /** @var FileResult[] */
        public array $files,
    ) {
    }

    public static function content(Content $content): self
    {
        $tags = array_map(
            fn (Tag $tag): TagResult => TagResult::tag($tag),
            iterator_to_array($content->tags()),
        );

        $files = array_map(
            fn (File $file): FileResult => FileResult::file($file),
            iterator_to_array($content->files()),
        );

        return new self(
            title: $content->title(),
            description: $content->description(),
            price: $content->price(),
            sales: $content->sales(),
            mode: $content->mode(),
            status: $content->status(),
            category: CategoryResult::category($content->category()),
            tags: $tags,
            files: $files,
        );
    }
}
