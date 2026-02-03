<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Upload;

use olml89\TelegramUserbot\Backend\Shared\Application\Command;

final readonly class UploadFileCommand implements Command
{
    public function __construct(
        public string $uploadId,
    ) {
    }
}
