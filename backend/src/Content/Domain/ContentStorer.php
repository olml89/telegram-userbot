<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use Throwable;

final readonly class ContentStorer
{
    public function __construct(
        private ContentRepository $contentRepository,
    ) {
    }

    /**
     * @throws ContentStorageException
     */
    public function store(Content $content): void
    {
        try {
            $this->contentRepository->store($content);
            $content->stored();
        } catch (Throwable $e) {
            throw ContentStorageException::store($content, $e);
        }
    }
}
