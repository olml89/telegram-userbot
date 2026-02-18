<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application;

use olml89\TelegramUserbot\Backend\Category\Application\CategoryResult;
use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Content\Domain\Mode\Mode;
use olml89\TelegramUserbot\Backend\Content\Domain\Status\Status;
use olml89\TelegramUserbot\Backend\File\Application\AudioResult;
use olml89\TelegramUserbot\Backend\File\Application\FileResult;
use olml89\TelegramUserbot\Backend\File\Application\FileResultFactory;
use olml89\TelegramUserbot\Backend\File\Application\ImageResult;
use olml89\TelegramUserbot\Backend\File\Application\VideoResult;
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

        /** @var array<int, FileResult|ImageResult|AudioResult|VideoResult> $files */
        public array $files,
    ) {}

    public static function content(Content $content, FileResultFactory $fileResultFactory): self
    {
        /** @var TagResult[] $tags */
        $tags = $content
            ->tags()
            ->map(fn(Tag $tag): TagResult => TagResult::tag($tag))
            ->toArray();

        /** @var array<int, FileResult|ImageResult|AudioResult|VideoResult> $files */
        $files = $content
            ->files()
            ->map(fn(File $file): FileResult|ImageResult|AudioResult|VideoResult => $fileResultFactory->create($file))
            ->toArray();

        return new self(
            title: $content->title()->value,
            description: $content->description()->value,
            price: $content->price()->value,
            sales: $content->sales(),
            mode: $content->mode(),
            status: $content->status(),
            category: CategoryResult::category($content->category()),
            tags: $tags,
            files: $files,
        );
    }
}
