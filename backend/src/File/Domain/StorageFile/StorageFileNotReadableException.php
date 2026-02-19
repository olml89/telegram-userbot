<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\StorageFile;

use RuntimeException;

final class StorageFileNotReadableException extends RuntimeException
{
    public function __construct(StorageFile $storageFile)
    {
        parent::__construct(
            sprintf(
                'File %s is not readable',
                $storageFile->getPathname(),
            ),
        );
    }
}
