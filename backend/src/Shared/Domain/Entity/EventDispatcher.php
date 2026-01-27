<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Entity;

interface EventDispatcher
{
    public function dispatch(Event $event): void;
}
