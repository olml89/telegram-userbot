<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Category\Domain;

use Symfony\Component\Uid\Uuid;

interface CategoryRepository
{
    /** @return Category[] */
    public function all(): array;

    public function get(Uuid $publicId): ?Category;
    public function store(Category $category): void;
}
