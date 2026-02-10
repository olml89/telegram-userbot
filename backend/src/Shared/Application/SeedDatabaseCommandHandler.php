<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Application;

use Generator;
use olml89\TelegramUserbot\Backend\Category\Application\CategoryResult;
use olml89\TelegramUserbot\Backend\Category\Domain\Category;
use olml89\TelegramUserbot\Backend\Category\Domain\CategoryStorageException;
use olml89\TelegramUserbot\Backend\Category\Domain\CategoryStorer;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\StringLengthException;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name\Name;
use Symfony\Component\Uid\Uuid;

final readonly class SeedDatabaseCommandHandler
{
    public function __construct(
        private CategoryStorer $categoryStorer,
    ) {
    }

    /**
     * @return Generator<CategoryResult>
     *
     * @throws StringLengthException
     * @throws CategoryStorageException
     */
    public function handle(SeedDatabaseCommand $command): Generator
    {
        foreach ($command->categoryNames as $categoryName) {
            $category = new Category(
                publicId: Uuid::v4(),
                name: new Name($categoryName),
            );

            $this->categoryStorer->store($category);

            yield CategoryResult::category($category);
        }
    }
}
