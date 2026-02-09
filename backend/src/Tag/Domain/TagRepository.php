<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name\Name;
use Symfony\Component\Uid\Uuid;

interface TagRepository
{
    public function get(Uuid $publicId): ?Tag;
    public function getByName(Name $name): ?Tag;

    /** @return Tag[] */
    public function search(?string $query, int $limit): array;

    public function store(Tag $tag): void;
}
