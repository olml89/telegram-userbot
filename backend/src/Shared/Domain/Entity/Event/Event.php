<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event;

use DateTimeImmutable;
use JsonSerializable;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;

interface Event extends JsonSerializable
{
    public function entity(): Entity;
    public function occurredAt(): DateTimeImmutable;

    /** @return array<string, mixed> */
    public function jsonSerialize(): array;
}
