<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\List;

use olml89\TelegramUserbot\Backend\Category\Application\CategoryResult;
use olml89\TelegramUserbot\Backend\Category\Domain\Category;
use olml89\TelegramUserbot\Backend\Category\Domain\CategoryRepository;
use olml89\TelegramUserbot\Backend\Content\Domain\Language;
use olml89\TelegramUserbot\Backend\Content\Domain\Mode;
use olml89\TelegramUserbot\Backend\Content\Domain\Status;

final readonly class ListContentCommandHandler
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {}

    public function handle(): ListContentResult
    {
        $categories = array_map(
            fn(Category $category): CategoryResult => CategoryResult::category($category),
            $this->categoryRepository->all(),
        );

        return new ListContentResult(
            categories: $categories,
            modes: Mode::cases(),
            statuses: Status::cases(),
            languages: Language::cases(),
        );
    }
}
