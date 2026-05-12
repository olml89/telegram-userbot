<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use Symfony\Component\Uid\Uuid;

final readonly class UnattachedFileFinder
{
    public function __construct(
        private UnattachedFileRepository $unattachedFileRepository,
    ) {}

    /**
     * @throws FileNotFoundException
     * @throws FileAlreadyAttachedException
     */
    public function find(Uuid $publicId): UnattachedFile
    {
        return $this->unattachedFileRepository->get($publicId) ?? throw new FileNotFoundException($publicId);
    }
}
