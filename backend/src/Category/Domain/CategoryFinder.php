<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Category\Domain;

use Symfony\Component\Uid\Uuid;

final readonly class CategoryFinder
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {}

    /**
     * @throws CategoryNotFoundException
     */
    public function find(Uuid $publicId): Category
    {
        return $this->categoryRepository->get($publicId) ?? throw new CategoryNotFoundException($publicId);
    }
}
