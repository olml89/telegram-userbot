<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Event;

interface EventEntityRepository
{
    public function store(EventEntity $eventEntity): void;
}
