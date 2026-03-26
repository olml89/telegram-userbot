<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\Paginate;

use Symfony\Component\Uid\Uuid;

final readonly class PaginateContentCommand
{
    public function __construct(
        public ?int $page,
        public ?string $search,
        public ?Uuid $categoryId,
        public ?string $mode,
    ) {}
}
