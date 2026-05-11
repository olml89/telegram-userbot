<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\Tag;

use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\Collection;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\HasArrayAccess;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagCollection;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagCollectionCountException;

/**
 * @implements Collection<string, Tag>
 */
final class TagManager extends TagCollection implements Collection
{
    use HasArrayAccess;

    /**
     * @throws TagCollectionCountException
     */
    public function add(Tag $tag): Tag
    {
        $this->checkBeforeInsert();
        $this->items[$tag->publicId()->toRfc4122()] = $tag;

        return $tag;
    }
}
