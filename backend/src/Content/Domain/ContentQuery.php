<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Category\Domain\Category;
use olml89\TelegramUserbot\Backend\Content\Domain\Mode\Mode;

final readonly class ContentQuery
{
    public function __construct(
        public ?string $search = null,
        public ?Category $category = null,
        public ?Mode $mode = null,
    ) {}
}
