<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;

/**
 * @template T of Entity
 * @extends EntityRepository<T>
 */
abstract class DoctrineRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(static::entityClass()));
    }

    /**
     * @return class-string<T>
     */
    abstract protected static function entityClass(): string;

    protected function removeEntity(Entity $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    protected function storeEntity(Entity $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
}
