<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Collection;

use Countable;
use IteratorAggregate;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @extends IteratorAggregate<TKey, TValue>
 */
interface ReadonlyCollection extends Countable, IteratorAggregate
{
    /**
     * @param callable(TValue): void $callback
     *
     * @return self<TKey, TValue>
     */
    public function each(callable $callback): self;

    /**
     * @param callable(TValue): bool $callback
     */
    public function exists(callable $callback): bool;

    /**
     * @param null|callable(TValue): bool $callback
     *
     * @return ReadonlyCollection<TKey, TValue>
     */
    public function filter(?callable $callback = null): ReadonlyCollection;

    public function isEmpty(): bool;

    /**
     * @template R
     *
     * @param callable(TValue): R $callback
     *
     * @return ReadonlyCollection<TKey, R>
     */
    public function map(callable $callback): ReadonlyCollection;

    /**
     * @template R
     *
     * @param callable(TValue, R): R $callback
     * @param ?R $initial
     *
     * @return R
     */
    public function reduce(callable $callback, mixed $initial = null): mixed;

    /**
     * @return array<TKey, TValue>
     */
    public function toArray(): array;

    /**
     * @return ReadonlyCollection<int, TValue>
     */
    public function values(): ReadonlyCollection;
}
