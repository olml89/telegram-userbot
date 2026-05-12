<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application\RemoveFile;

use Symfony\Component\Uid\Uuid;

final readonly class RemoveContentFileCommand
{
    public function __construct(
        public Uuid $contentId,
        public Uuid $fileId,
    ) {}
}
