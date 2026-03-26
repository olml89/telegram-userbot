<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\Collection;

/**
 * @extends Collection<Content>
 */
final class PaginatedContentCollection extends Collection
{
    public function __construct(
        public readonly int $totalCount,
        Content ...$contents,
    ) {
        foreach ($contents as $content) {
            $this->add($content);
        }
    }

    private function add(Content $content): void
    {
        $this->items[] = $content;
    }
}
