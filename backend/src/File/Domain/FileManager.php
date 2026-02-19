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
    public function consume(File $file, Upload $upload): void;

    public function exists(File $file): bool;

    /** @throws StorageFileNotReadableException */
    public function mediaFile(File|FileName $subject): StorageFile;
    public function path(File|FileName $subject): string;
    public function remove(File $file): void;
}
