<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

final readonly class ContentFinder
{
    public function __construct(
        private ContentRepository $contentRepository,
    ) {}

    /**
     * @throws ContentNotFoundException
     */
    public function findByTitle(string $title): Content
    {
        return $this->contentRepository->getByTitle($title) ?? throw ContentNotFoundException::title($title);
    }
}
