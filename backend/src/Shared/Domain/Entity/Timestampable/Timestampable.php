<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Timestampable;

use DateTimeImmutable;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;

interface Timestampable extends Entity
{
    public function createdAt(): DateTimeImmutable;
    public function update(): static;
    public function updatedAt(): DateTimeImmutable;
}
