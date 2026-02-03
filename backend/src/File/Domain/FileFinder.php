<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use Symfony\Component\Uid\Uuid;

final readonly class FileFinder
{
    public function __construct(
        private FileRepository $fileRepository,
    ) {
    }

    /**
     * @throws FileNotFoundException
     */
    public function find(Uuid $publicId): File
    {
        return $this->fileRepository->get($publicId) ?? throw new FileNotFoundException($publicId);
    }
}
