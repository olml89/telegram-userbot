<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Collection;

/**
 * @template T
 *
 * @extends Collection<T>
 */
final class GenericCollection extends Collection
{
    /**
     * @param T ...$items
     */
    public function __construct(...$items)
    {
        foreach ($items as $item) {
            $this->items[] = $item;
        }
    }
}
