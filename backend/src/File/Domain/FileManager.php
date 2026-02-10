<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\File\Domain\Upload\Upload;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumptionException;

interface FileManager
{
    /** @throws UploadConsumptionException */
    public function consume(File $file, Upload $upload): File;

    public function exists(File $file): bool;
    public function remove(File $file): void;
}
