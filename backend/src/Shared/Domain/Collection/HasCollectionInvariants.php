<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Collection;

use Countable;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\CollectionCountException;

/**
 * @mixin Countable
 */
trait HasCollectionInvariants
{
    private ?CollectionCountException $invariant = null;

    protected function setInvariant(?CollectionCountException $invariant): void
    {
        $this->invariant = $invariant;
    }

    protected function checkInvariants(): void
    {
        $this->invariant?->assertNotLessThanMin($this->count());
        $this->invariant?->assertNotGreaterThanMax($this->count());
    }

    protected function checkBeforeInsert(): void
    {
        $this->invariant?->assertNotGreaterThanMax($this->count() + 1);
    }

    protected function checkBeforeDelete(): void
    {
        $this->invariant?->assertNotLessThanMin($this->count() - 1);
    }
}
