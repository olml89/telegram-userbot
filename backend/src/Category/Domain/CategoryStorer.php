<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Category\Domain;

use Throwable;

final readonly class CategoryStorer
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {}

    /**
     * @throws CategoryStorageException
     */
    public function store(Category $category): void
    {
        try {
            $this->categoryRepository->store($category);
            $category->stored();
        } catch (Throwable $e) {
            throw CategoryStorageException::store($category, $e);
        }
    }
}
