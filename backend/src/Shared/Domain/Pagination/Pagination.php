<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Pagination;

final readonly class Pagination
{
    public function __construct(
        public int $perPage,
        public int $page = 1,
    ) {}
}
