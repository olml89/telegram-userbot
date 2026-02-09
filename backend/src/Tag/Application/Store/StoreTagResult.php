<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Application\Store;

use olml89\TelegramUserbot\Backend\Shared\Application\Result\IsResult;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\Result;
use olml89\TelegramUserbot\Backend\Tag\Application\TagResult;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;

final readonly class StoreTagResult implements Result
{
    use IsResult;

    public function __construct(
        public TagResult $tag,
        public bool $created,
    ) {
    }

    public static function tag(Tag $tag, bool $created): self
    {
        return new self(TagResult::tag($tag), $created);
    }

    public static function found(Tag $tag): self
    {
        return self::tag($tag, created: false);
    }

    public static function created(Tag $tag): self
    {
        return self::tag($tag, created: true);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->tag->jsonSerialize();
    }
}
