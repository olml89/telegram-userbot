<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Collection;

use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * @template T
 *
 * @implements IteratorAggregate<int, T>
 */
abstract class Collection implements IteratorAggregate, Countable
{
    /**
     * @var T[]
     */
    protected array $items = [];

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @param callable(T): void $callback
     *
     * @return Collection<T>
     */
    public function each(callable $callback): Collection
    {
        foreach ($this->items as $item) {
            $callback($item);
        }

        return $this;
    }

    /**
     * @param null|callable(T): bool $callback
     *
     * @return GenericCollection<T>
     */
    public function filter(?callable $callback = null): Collection
    {
        /** @param T $item */
        $callback ??= static fn (mixed $item): bool => !is_null($item);

        /** @var T[] $filtered */
        $filtered = array_filter($this->items, $callback);

        return new GenericCollection(...array_values($filtered));
    }

    /**
     * @return ArrayIterator<int, T>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @template R
     *
     * @param callable(T): R $callback
     *
     * @return GenericCollection<R>
     */
    public function map(callable $callback): GenericCollection
    {
        return new GenericCollection(...array_map($callback, $this->items));
    }

    /**
     * @return T[]
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
