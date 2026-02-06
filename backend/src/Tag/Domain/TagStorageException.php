<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Domain;

use Exception;
use Throwable;

final class TagStorageException extends Exception
{
    private function __construct(string $message, Throwable $previous)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function store(Tag $tag, Throwable $previous): self
    {
        return new self(
            message: sprintf(
                'Error storing tag %s',
                $tag->publicId()->toRfc4122(),
            ),
            previous: $previous,
        );
    }

    public static function remove(Tag $tag, Throwable $previous): self
    {
        return new self(
            message: sprintf(
                'Error removing tag %s',
                $tag->publicId()->toRfc4122(),
            ),
            previous: $previous,
        );
    }
}
