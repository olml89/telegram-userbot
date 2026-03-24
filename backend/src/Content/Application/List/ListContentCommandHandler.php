<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\List;

use olml89\TelegramUserbot\Backend\Category\Application\CategoryResult;
use olml89\TelegramUserbot\Backend\Category\Domain\Category;
use olml89\TelegramUserbot\Backend\Category\Domain\CategoryRepository;
use olml89\TelegramUserbot\Backend\Content\Application\Paginate\ContentPaginator;
use olml89\TelegramUserbot\Backend\Content\Domain\Language\Language;
use olml89\TelegramUserbot\Backend\Content\Domain\Mode\Mode;
use olml89\TelegramUserbot\Backend\Content\Domain\Status\Status;
use olml89\TelegramUserbot\Backend\Shared\Application\Pagination\PaginationException;

final readonly class ListContentCommandHandler
{
    public function __construct(
        private ContentPaginator $contentPaginator,
        private CategoryRepository $categoryRepository,
    ) {}

    /**
     * @throws PaginationException
     */
    public function handle(): ListContentResult
    {
        $contents = $this->contentPaginator->paginate();

        $categories = $this
            ->categoryRepository
            ->all()
            ->map(fn(Category $category): CategoryResult => CategoryResult::category($category));

        return new ListContentResult(
            contents: $contents,
            categories: $categories->toArray(),
            modes: Mode::cases(),
            statuses: Status::cases(),
            languages: Language::cases(),
        );
    }
}
