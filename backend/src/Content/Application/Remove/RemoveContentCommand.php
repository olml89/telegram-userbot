<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\Remove;

use Symfony\Component\Uid\Uuid;

final readonly class RemoveContentCommand
{
    public function __construct(
        public Uuid $publicId,
    ) {}
}
