<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\ContentFile;

use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentRepository;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentStorageException;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotFoundException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\CollectionCountException;
use Symfony\Component\Uid\Uuid;
use Throwable;

final readonly class ContentFileRemover
{
    public function __construct(
        private ContentRepository $contentRepository,
    ) {}

    /**
     * @throws CollectionCountException
     * @throws FileNotFoundException
     * @throws ContentStorageException
     */
    public function remove(Content $content, Uuid $fileId): void
    {
        $content->removeFile($fileId);

        try {
            $this->contentRepository->store($content);
        } catch (Throwable $e) {
            throw ContentStorageException::remove($content, $e);
        }
    }
}
