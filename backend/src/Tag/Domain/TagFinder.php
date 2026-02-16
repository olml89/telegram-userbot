<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Domain;

use Symfony\Component\Uid\Uuid;

final readonly class TagFinder
{
    public function __construct(
        private TagRepository $tagRepository,
    ) {}

    /**
     * @throws TagNotFoundException
     */
    public function find(Uuid $publicId): Tag
    {
        return $this->tagRepository->get($publicId) ?? throw new TagNotFoundException($publicId);
    }
}
