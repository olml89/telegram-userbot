<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Doctrine;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileRepository;
use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Doctrine\DoctrineRepository;
use Symfony\Component\Uid\Uuid;

/**
 * @extends DoctrineRepository<File>
 */
final class DoctrineFileRepository extends DoctrineRepository implements FileRepository
{
    protected static function entityClass(): string
    {
        return File::class;
    }

    public function get(Uuid $publicId): ?File
    {
        return $this->findOneBy(['publicId' => $publicId]);
    }

    public function remove(File $file): void
    {
        $this->removeEntity($file);
    }

    public function store(File $file): void
    {
        $this->storeEntity($file);
    }
}
