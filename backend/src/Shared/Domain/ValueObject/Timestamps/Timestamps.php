<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Timestamps;

use DateTimeImmutable;

final class Timestamps
{
    protected readonly DateTimeImmutable $createdAt;
    protected DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function update(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
