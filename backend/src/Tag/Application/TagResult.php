<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Application;

use olml89\TelegramUserbot\Backend\Shared\Application\Result\IsResult;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\Result;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;

readonly class TagResult implements Result
{
    use IsResult;

    public function __construct(
        public string $id,
        public string $name,
    ) {}

    public static function tag(Tag $tag): self
    {
        return new self(
            id: $tag->publicId()->toRfc4122(),
            name: $tag->name()->value,
        );
    }
}
