<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile;

use olml89\TelegramUserbot\Backend\Content\Domain\File;

interface UploadedFile
{
    /** @throws UploadedFileException */
    public function save(string $contentDirectory): File;
}
