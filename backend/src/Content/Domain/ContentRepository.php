<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Pagination\Pagination;
use Symfony\Component\Uid\Uuid;

interface ContentRepository
{
    public function get(Uuid $publicId): ?Content;
    public function getByTitle(string $title): ?Content;
    public function paginate(ContentQuery $query, Pagination $pagination): PaginatedContentCollection;
    public function store(Content $content): void;
}
