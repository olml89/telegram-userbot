<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile\UploadedFile;
use olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile\UploadedFileException;

interface ContentFileManager
{
    /** @throws UploadedFileException */
    public function save(UploadedFile $uploadedFile): File;

    public function exists(File $file): bool;
    public function remove(File $file): void;
}
