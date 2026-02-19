<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\StorageFile;

use RuntimeException;
use Throwable;

final class StorageFileNotReadableException extends RuntimeException
{
    public function __construct(string $path, Throwable $previous)
    {
        parent::__construct(
            message: sprintf(
                'File %s is not readable',
                $path,
            ),
            previous: $previous,
        );
    }
}
