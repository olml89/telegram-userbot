<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use Throwable;

final readonly class ContentRemover
{
    public function __construct(
        private ContentRepository $contentRepository,
    ) {}

    /**
     * @throws ContentStorageException
     */
    public function remove(Content $content): void
    {
        try {
            $this->contentRepository->remove($content);
            $content->removed();
        } catch (Throwable $e) {
            throw ContentStorageException::remove($content, $e);
        }
    }
}
