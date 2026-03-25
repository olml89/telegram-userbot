<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Application\Pagination;

use ArrayIterator;
use IteratorAggregate;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\IsResult;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\Result;
use Traversable;

/**
 * @template T of Result
 * @implements IteratorAggregate<int, T>
 */
final readonly class PaginationResult implements Result, IteratorAggregate
{
    use IsResult;

    /**
     * @var array<int, T>
     */
    private array $list;

    /**
     * @param T ...$results
     */
    public function __construct(
        public int $page,
        public int $perPage,
        public int $totalCount,
        Result ...$results,
    ) {
        $this->list = array_values($results);

        if ($this->page < 1) {
            throw new PaginationException('page must be greater than 0');
        }

        if ($this->perPage < 1) {
            throw new PaginationException('perPage must be greater than 0');
        }
    }

    /**
     * @return Traversable<int, T>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->list);
    }
}
