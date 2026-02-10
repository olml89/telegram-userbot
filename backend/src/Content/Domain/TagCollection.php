<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\Collection;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\CollectionCountException;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;

/**
 * @extends Collection<Tag>
 */
final class TagCollection extends Collection
{
    private const int MIN_COUNT = 1;
    private const int MAX_COUNT = 10;

    /**
     * @throws CollectionCountException
     */
    public function __construct(Tag ...$tags)
    {
        foreach ($tags as $tag) {
            $this->add($tag);
        }

        if ($this->count() < self::MIN_COUNT) {
            throw new CollectionCountException(self::MIN_COUNT, self::MAX_COUNT);
        }
    }

    /**
     * @throws CollectionCountException
     */
    public function add(Tag $tag): self
    {
        if ($this->count() > self::MAX_COUNT) {
            throw new CollectionCountException(self::MIN_COUNT, self::MAX_COUNT);
        }

        $this->items[] = $tag;

        return $this;
    }
}
