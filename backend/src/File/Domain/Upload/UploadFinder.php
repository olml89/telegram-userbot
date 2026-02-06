<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Upload;

interface UploadFinder
{
    /** @throws UploadNotFoundException */
    public function find(string $uploadId): Upload;
}
