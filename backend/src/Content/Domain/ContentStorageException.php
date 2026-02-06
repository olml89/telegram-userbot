<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use Exception;
use Throwable;

final class ContentStorageException extends Exception
{
    private function __construct(string $message, Throwable $previous)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function store(Content $content, Throwable $previous): self
    {
        return new self(
            message: sprintf(
                'Error storing content %s',
                $content->publicId()->toRfc4122(),
            ),
            previous: $previous,
        );
    }

    public static function remove(Content $content, Throwable $previous): self
    {
        return new self(
            message: sprintf(
                'Error removing content %s',
                $content->publicId()->toRfc4122(),
            ),
            previous: $previous,
        );
    }
}
