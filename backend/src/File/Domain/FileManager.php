<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileName;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFile;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFileNotReadableException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\Upload;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumptionException;

interface FileManager
{
    /** @throws UploadConsumptionException */
    public function consume(UnattachedFile $unattachedFile, Upload $upload): void;

    /** @throws StorageFileNotReadableException */
    public function storageFile(File|FileName $subject): StorageFile;
    public function remove(File|StorageFile $file): void;
}
