<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Application\Store;

final readonly class StoreTagCommand
{
    public function __construct(
        public string $name,
    ) {
    }
}
