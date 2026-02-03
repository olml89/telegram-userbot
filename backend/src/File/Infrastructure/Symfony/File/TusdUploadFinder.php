<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\File;

use olml89\TelegramUserbot\Backend\File\Domain\Upload\Upload;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadFinder;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadNotFoundException;

final readonly class TusdUploadFinder implements UploadFinder
{
    public function __construct(
        private string $uploadDirectory,
    ) {
    }

    /**
     * @throws UploadNotFoundException
     */
    public function find(string $uploadId): Upload
    {
        return new TusdUpload($this->uploadDirectory, $uploadId);
    }
}
