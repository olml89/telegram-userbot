<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Upload;

use olml89\TelegramUserbot\Backend\File\Domain\File;

final readonly class UploadConsumer
{
    public function __construct(
        private string $contentDirectory,
    ) {
    }

    /**
     * @throws UploadConsumptionException
     */
    public function consume(Upload $upload): File
    {
        return File::fromUpload($upload, $this->contentDirectory);
    }
}
