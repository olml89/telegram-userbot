<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\ReadonlyArrayCollection;

/**
 * @extends ReadonlyArrayCollection<int, Content>
 */
final class PaginatedContentCollection extends ReadonlyArrayCollection
{
    public function __construct(
        public readonly int $totalCount,
        Content ...$contents,
    ) {
        parent::__construct(array_values($contents));
    }
}
