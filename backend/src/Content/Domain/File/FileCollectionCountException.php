<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\File;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\CollectionCountException;

final class FileCollectionCountException extends CollectionCountException
{
    protected static function elementsName(): string
    {
        return 'files';
    }
}
