<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Application\Search;

use olml89\TelegramUserbot\Backend\Tag\Application\TagResult;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagRepository;

final readonly class SearchTagCommandHandler
{
    public function __construct(
        private TagRepository $tagRepository,
    ) {
    }

    /**
     * @return TagResult[]
     */
    public function handle(SearchTagCommand $command): array
    {
        return array_map(
            fn (Tag $tag): TagResult => TagResult::tag($tag),
            $this->tagRepository->search($command->query, $command->limit),
        );
    }
}
