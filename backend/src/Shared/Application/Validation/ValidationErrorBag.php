<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Application\Validation;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, string>
 */
final class ValidationErrorBag implements IteratorAggregate
{
    /**
     * @var string[]
     */
    private array $errorMessages = [];

    public function add(string $errorMessage): void
    {
        $this->errorMessages[] = $errorMessage;
    }

    /**
     * @return Traversable<string>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->errorMessages);
    }
}
