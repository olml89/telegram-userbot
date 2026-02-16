<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Upload;

final readonly class UploadFileCommand
{
    public function __construct(
        public string $uploadId,
    ) {
    }
}
