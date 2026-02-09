<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Infrastructure\Doctrine;

use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name\Name;
use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Doctrine\DoctrineRepository;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagRepository;
use Symfony\Component\Uid\Uuid;

/**
 * @extends DoctrineRepository<Tag>
 */
final class DoctrineTagRepository extends DoctrineRepository implements TagRepository
{
    protected static function entityClass(): string
    {
        return Tag::class;
    }

    public function get(Uuid $publicId): ?Tag
    {
        return $this->findOneBy([
            'publicId' => $publicId,
        ]);
    }

    public function getByName(Name $name): ?Tag
    {
        return $this->findOneBy([
            'name' => $name,
        ]);
    }

    /**
     * @return Tag[]
     */
    public function search(?string $query, int $limit): array
    {
        $criteria = is_null($query) ? [] : [
            'name' => $query,
        ];

        return $this->searchEntity(
            criteria: $criteria,
            limit: $limit,
        );
    }

    public function store(Tag $tag): void
    {
        $this->storeEntity($tag);
    }
}
