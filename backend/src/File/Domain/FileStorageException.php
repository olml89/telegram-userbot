<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use Exception;
use Throwable;

final class FileStorageException extends Exception
{
    private function __construct(string $message, Throwable $previous)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function store(UnattachedFile $unattachedFile, Throwable $previous): self
    {
        return new self(
            message: sprintf(
                'Error storing file %s',
                $unattachedFile->file()->publicId()->toRfc4122(),
            ),
            previous: $previous,
        );
    }

    public static function remove(UnattachedFile $unattachedFile, Throwable $previous): self
    {
        return new self(
            message: sprintf(
                'Error removing file %s',
                $unattachedFile->file()->publicId()->toRfc4122(),
            ),
            previous: $previous,
        );
    }
}
