<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Display;

use Symfony\Component\Uid\Uuid;

final readonly class DisplayCommand
{
    public function __construct(
        public Uuid $publicId,
    ) {}
}
