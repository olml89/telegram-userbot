<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Doctrine;

use Doctrine\ORM\QueryBuilder;
use IteratorAggregate;
use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentFile\ContentFile;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentQuery;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentRepository;
use olml89\TelegramUserbot\Backend\Content\Domain\PaginatedContentCollection;
use olml89\TelegramUserbot\Backend\Shared\Domain\Pagination\Pagination;
use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Doctrine\DoctrineRepository;
use Symfony\Component\Uid\Uuid;
use Traversable;

/**
 * @extends DoctrineRepository<Content>
 */
final class DoctrineContentRepository extends DoctrineRepository implements ContentRepository
{
    protected static function entityClass(): string
    {
        return Content::class;
    }

    public function get(Uuid $publicId): ?Content
    {
        return $this->findOneBy([
            'publicId' => $publicId,
        ]);
    }

    public function getByTitle(string $title): ?Content
    {
        return $this->findOneBy([
            'title' => $title,
        ]);
    }

    private function applyContentQuery(QueryBuilder $queryBuilder, ContentQuery $query): void
    {
        if (!is_null($query->search)) {
            $match = mb_strtolower($query->search);

            $queryBuilder
                ->leftJoin('content.tags', 'tags')
                ->andWhere(
                    $queryBuilder->expr()->orX(
                        'LOWER(content.title) LIKE :search',
                        'LOWER(content.description) LIKE :search',
                        'LOWER(tags.name) LIKE :search',
                    ),
                )
                ->setParameter('search', sprintf('%%%s%%', $match));
        }

        if (!is_null($query->category)) {
            $queryBuilder
                ->andWhere('content.category = :category')
                ->setParameter('category', $query->category);
        }

        if (!is_null($query->mode)) {
            $queryBuilder
                ->andWhere('content.mode = :mode')
                ->setParameter('mode', $query->mode);
        }
    }

    public function paginate(ContentQuery $query, Pagination $pagination): PaginatedContentCollection
    {
        $resultsQueryBuilder = $this->createQueryBuilder('content');
        $this->applyContentQuery($resultsQueryBuilder, $query);

        /** @var Content[] $contents */
        $contents = $resultsQueryBuilder
            ->setFirstResult(($pagination->page - 1) * $pagination->perPage)
            ->setMaxResults($pagination->perPage)
            ->orderBy('content.timestamps.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $countQueryBuilder = $this
            ->createQueryBuilder('content')
            ->select('COUNT(DISTINCT content.id)');

        $this->applyContentQuery($countQueryBuilder, $query);

        /** @var int $totalContentsCount */
        $totalContentsCount = $countQueryBuilder
            ->getQuery()
            ->getSingleScalarResult();

        return new PaginatedContentCollection(
            $totalContentsCount,
            ...$contents,
        );
    }

    public function remove(Content $content): void
    {
        $this->removeEntity($content);
    }

    public function store(Content $content): void
    {
        /**
         * Remove orphaned ContentFiles
         */
        $uow = $this->getEntityManager()->getUnitOfWork();
        $originalData = $uow->getOriginalEntityData($content);

        if (isset($originalData['contentFiles'])) {
            /** @var IteratorAggregate<ContentFile> $originalContentFiles */
            $originalContentFiles = $originalData['contentFiles'];

            /**
             * @var ContentFile $originalContentFile
             */
            foreach ($originalContentFiles as $originalContentFile) {
                $exists = fn(ContentFile $contentFile): bool => $contentFile->equals($originalContentFile);

                if (!$content->contentFiles()->exists($exists)) {
                    $this->getEntityManager()->remove($originalContentFile);
                }
            }
        }

        $this->storeEntity($content);
    }
}
