<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Doctrine;

use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentRepository;
use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Doctrine\DoctrineRepository;

/**
 * @extends DoctrineRepository<Content>
 */
final class DoctrineContentRepository extends DoctrineRepository implements ContentRepository
{
    protected static function entityClass(): string
    {
        return Content::class;
    }

    public function store(Content $content): void
    {
        $this->storeEntity($content);
    }
}
