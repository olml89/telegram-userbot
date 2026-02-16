<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\Tag;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\CollectionCountException;

final class TagCollectionCountException extends CollectionCountException
{
    protected static function elementsName(): string
    {
        return 'tags';
    }
}
