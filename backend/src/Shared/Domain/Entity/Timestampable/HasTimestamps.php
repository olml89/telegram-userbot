<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Timestampable;

use DateTimeImmutable;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Timestamps\Timestamps;

/**
 * @mixin Timestampable
 */
trait HasTimestamps
{
    protected readonly Timestamps $timestamps;

    public function createdAt(): DateTimeImmutable
    {
        return $this->timestamps->createdAt();
    }

    public function update(): static
    {
        $this->timestamps->update();

        return $this;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->timestamps->updatedAt();
    }
}
