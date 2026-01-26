<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile;

use olml89\TelegramUserbot\Backend\Content\Domain\File;

interface UploadedFileSaver
{
    /** @throws UploadedFileException */
    public function save(UploadedFile $uploadedFile): File;
}
