<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Category\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\ReadonlyCollection;
use Symfony\Component\Uid\Uuid;

interface CategoryRepository
{
    /** @return ReadonlyCollection<int, Category> */
    public function all(): ReadonlyCollection;

    public function get(Uuid $publicId): ?Category;
    public function store(Category $category): void;
}
