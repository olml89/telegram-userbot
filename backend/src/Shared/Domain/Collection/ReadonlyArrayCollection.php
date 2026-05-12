<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Collection;

use ArrayIterator;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @implements ReadonlyCollection<TKey, TValue>
 */
class ReadonlyArrayCollection implements ReadonlyCollection
{
    public function __construct(
        /**
         * @var array<TKey, TValue>
         */
        protected array $items = [],
    ) {}

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return ArrayIterator<TKey, TValue>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @param callable(TValue): void $callback
     *
     * @return self<TKey, TValue>
     */
    public function each(callable $callback): self
    {
        foreach ($this->items as $item) {
            $callback($item);
        }

        return $this;
    }

    /**
     * @param ?callable(TValue): bool $callback
     *
     * @return ReadonlyCollection<TKey, TValue>
     */
    public function filter(?callable $callback = null): ReadonlyCollection
    {
        /** @param TValue $item */
        $callback ??= static fn(mixed $item): bool => !is_null($item);

        /** @var array<TKey, TValue> $filtered */
        $filtered = array_filter($this->items, $callback);

        return new ReadonlyArrayCollection($filtered);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @template R
     *
     * @param callable(TValue): R $callback
     *
     * @return ReadonlyCollection<TKey, R>
     */
    public function map(callable $callback): ReadonlyCollection
    {
        /** @var array<TKey, R> $mapped */
        $mapped = array_map($callback, $this->items);

        return new ReadonlyArrayCollection($mapped);
    }

    /**
     * @template R
     *
     * @param callable(?R, TValue): R $callback
     * @param ?R $initial
     *
     * @return ?R
     */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * @return array<TKey, TValue>
     */
    public function toArray(): array
    {
        return array_combine(
            array_keys($this->items),
            array_values($this->items),
        );
    }

    /**
     * @return ReadonlyCollection<int, TValue>
     */
    public function values(): ReadonlyCollection
    {
        /** @var array<int, TValue> $values */
        $values = array_values($this->items);

        return new ReadonlyArrayCollection($values);
    }
}
