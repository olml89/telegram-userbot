<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Size;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

final class SizeException extends InvariantException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function empty(): self
    {
        return new self('A file cannot be empty');
    }

    public static function tooBig(int $maxBytes): self
    {
        return new self(
            sprintf(
                'File size cannot exceed %d bytes',
                $maxBytes
            ),
        );
    }
}
