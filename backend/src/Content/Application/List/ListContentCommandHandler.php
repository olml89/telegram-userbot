<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\List;

use olml89\TelegramUserbot\Backend\Category\Application\CategoryResult;
use olml89\TelegramUserbot\Backend\Category\Domain\Category;
use olml89\TelegramUserbot\Backend\Category\Domain\CategoryRepository;
use olml89\TelegramUserbot\Backend\Content\Application\ContentResult;
use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentRepository;
use olml89\TelegramUserbot\Backend\Content\Domain\Language\Language;
use olml89\TelegramUserbot\Backend\Content\Domain\Mode\Mode;
use olml89\TelegramUserbot\Backend\Content\Domain\Status\Status;

final readonly class ListContentCommandHandler
{
    public function __construct(
        private ContentRepository $contentRepository,
        private CategoryRepository $categoryRepository,
    ) {}

    public function handle(): ListContentResult
    {
        $contents = $this
            ->contentRepository
            ->all()
            ->map(
                fn(Content $content): ContentResult => ContentResult::content($content),
            )
            ->toArray();

        $categories = $this
            ->categoryRepository
            ->all()
            ->map(
                fn(Category $category): CategoryResult => CategoryResult::category($category),
            )
            ->toArray();

        return new ListContentResult(
            contents: $contents,
            categories: $categories,
            modes: Mode::cases(),
            statuses: Status::cases(),
            languages: Language::cases(),
        );
    }
}
