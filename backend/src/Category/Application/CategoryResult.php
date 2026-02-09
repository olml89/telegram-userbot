<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Category\Application;

use olml89\TelegramUserbot\Backend\Category\Domain\Category;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\IsResult;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\Result;

final readonly class CategoryResult implements Result
{
    use IsResult;

    public function __construct(
        public string $publicId,
        public string $name,
    ) {
    }

    public static function category(Category $category): self
    {
        return new self(
            publicId: $category->publicId()->toRfc4122(),
            name: $category->name(),
        );
    }
}
