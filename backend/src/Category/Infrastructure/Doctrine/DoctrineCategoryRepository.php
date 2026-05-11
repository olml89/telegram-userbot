<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Category\Infrastructure\Doctrine;

use olml89\TelegramUserbot\Backend\Category\Domain\Category;
use olml89\TelegramUserbot\Backend\Category\Domain\CategoryRepository;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\ReadonlyArrayCollection;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\ReadonlyCollection;
use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Doctrine\DoctrineRepository;
use Symfony\Component\Uid\Uuid;

/**
 * @extends DoctrineRepository<Category>
 */
final class DoctrineCategoryRepository extends DoctrineRepository implements CategoryRepository
{
    protected static function entityClass(): string
    {
        return Category::class;
    }

    /**
     * @return ReadonlyCollection<int, Category>
     */
    public function all(): ReadonlyCollection
    {
        return new ReadonlyArrayCollection($this->findAll());
    }

    public function get(Uuid $publicId): ?Category
    {
        return $this->findOneBy([
            'publicId' => $publicId,
        ]);
    }

    public function store(Category $category): void
    {
        $this->storeEntity($category);
    }
}
