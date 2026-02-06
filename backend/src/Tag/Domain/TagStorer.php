<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Domain;

use Throwable;

final readonly class TagStorer
{
    public function __construct(
        private TagRepository $tagRepository,
    ) {
    }

    /**
     * @throws TagStorageException
     */
    public function store(Tag $tag): void
    {
        try {
            $this->tagRepository->store($tag);
            $tag->stored();
        } catch (Throwable $e) {
            throw TagStorageException::store($tag, $e);
        }
    }
}
