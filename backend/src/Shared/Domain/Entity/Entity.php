<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Entity;

use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event\Event;
use Symfony\Component\Uid\Uuid;

interface Entity
{
    public function id(): int;
    public function publicId(): Uuid;
    public function record(Event $event): static;

    /** @return Event[] */
    public function events(): array;
}
