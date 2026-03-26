<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Category\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\Collection;
use Symfony\Component\Uid\Uuid;

interface CategoryRepository
{
    /** @return Collection<Category> */
    public function all(): Collection;

    public function get(Uuid $publicId): ?Category;
    public function store(Category $category): void;
}
