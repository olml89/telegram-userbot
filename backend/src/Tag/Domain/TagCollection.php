<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\HasCollectionInvariants;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\ReadonlyArrayCollection;

/**
 * @extends ReadonlyArrayCollection<string, Tag>
 */
class TagCollection extends ReadonlyArrayCollection
{
    use HasCollectionInvariants;

    /**
     * @throws TagCollectionCountException
     */
    public function __construct(Tag ...$tags)
    {
        parent::__construct();

        $this->setInvariant(new TagCollectionCountException());

        foreach ($tags as $tag) {
            $this->checkBeforeInsert();
            $this->items[$tag->publicId()->toRfc4122()] = $tag;
        }

        $this->checkInvariants();
    }
}
