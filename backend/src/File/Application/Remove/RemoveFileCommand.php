<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Remove;

use Symfony\Component\Uid\Uuid;

final readonly class RemoveFileCommand
{
    public function __construct(
        public Uuid $publicId,
    ) {
    }
}
