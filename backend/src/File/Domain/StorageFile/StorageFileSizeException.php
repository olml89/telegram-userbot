<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\StorageFile;

use RuntimeException;
use Throwable;

final class StorageFileSizeException extends RuntimeException
{
    public function __construct(StorageFile $file, ?Throwable $previous = null)
    {
        parent::__construct(
            message: sprintf(
                'File %s size is not readable',
                $file->getPathname(),
            ),
            previous: $previous,
        );
    }
}
