<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\DisplayThumbnail;

use Symfony\Component\Uid\Uuid;

final readonly class DisplayThumbnailCommand
{
    public function __construct(
        public Uuid $publicId,
    ) {}
}
