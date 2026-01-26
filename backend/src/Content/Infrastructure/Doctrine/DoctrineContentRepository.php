<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentRepository;

/**
 * @extends EntityRepository<Content>
 */
final class DoctrineContentRepository extends EntityRepository implements ContentRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, new ClassMetadata(Content::class));
    }

    public function store(Content $content): void
    {
        $this->getEntityManager()->persist($content);
        $this->getEntityManager()->flush();
    }
}
