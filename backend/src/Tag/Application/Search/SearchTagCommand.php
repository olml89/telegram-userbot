<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Application\Search;

final readonly class SearchTagCommand
{
    public function __construct(
        public ?string $query,
        public int $limit = 20,
    ) {}
}
