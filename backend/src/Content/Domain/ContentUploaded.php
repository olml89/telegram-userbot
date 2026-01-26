<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\EntityEvent;

final readonly class ContentUploaded extends EntityEvent
{
    public function __construct(
        public Content $content,
    ) {
    }
}
