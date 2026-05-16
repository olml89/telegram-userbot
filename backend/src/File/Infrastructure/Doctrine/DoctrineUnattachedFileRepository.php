<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Doctrine;

use olml89\TelegramUserbot\Backend\Content\Domain\ContentFile\ContentFile;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileAlreadyAttachedException;
use olml89\TelegramUserbot\Backend\File\Domain\UnattachedFile;
use olml89\TelegramUserbot\Backend\File\Domain\UnattachedFileRepository;
use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Doctrine\DoctrineRepository;
use Symfony\Component\Uid\Uuid;

/**
 * @extends DoctrineRepository<File>
 */
final class DoctrineUnattachedFileRepository extends DoctrineRepository implements UnattachedFileRepository
{
    protected static function entityClass(): string
    {
        return File::class;
    }

    /**
     * @throws FileAlreadyAttachedException
     */
    public function get(Uuid $publicId): ?UnattachedFile
    {
        $file = $this->findOneBy([
            'publicId' => $publicId,
        ]);

        if (is_null($file)) {
            return null;
        }

        $isAttached = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('1')
            ->from(ContentFile::class, 'cf')
            ->where('cf.file = :file')
            ->setParameter('file', $file)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($isAttached !== null) {
            throw new FileAlreadyAttachedException($file);
        }

        return new UnattachedFile($file);
    }

    public function remove(UnattachedFile $unattachedFile): void
    {
        $this->removeEntity($unattachedFile->file());
    }

    public function store(UnattachedFile $unattachedFile): void
    {
        $this->storeEntity($unattachedFile->file());
    }
}
