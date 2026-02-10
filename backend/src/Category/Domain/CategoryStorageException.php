<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Category\Domain;

use Exception;
use Throwable;

final class CategoryStorageException extends Exception
{
    private function __construct(string $message, Throwable $previous)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function store(Category $category, Throwable $previous): self
    {
        return new self(
            message: sprintf(
                'Error storing category %s',
                $category->publicId()->toRfc4122(),
            ),
            previous: $previous,
        );
    }

    public static function remove(Category $category, Throwable $previous): self
    {
        return new self(
            message: sprintf(
                'Error removing category %s',
                $category->publicId()->toRfc4122(),
            ),
            previous: $previous,
        );
    }
}
