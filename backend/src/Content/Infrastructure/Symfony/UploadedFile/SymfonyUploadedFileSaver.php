<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\UploadedFile;

use olml89\TelegramUserbot\Backend\Content\Domain\File;
use olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile\UploadedFile;
use olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile\UploadedFileSaver;
use olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile\UploadedFileException;

final readonly class SymfonyUploadedFileSaver implements UploadedFileSaver
{
    public function __construct(
        private string $contentDirectory,
    ) {
    }

    /**
     * @throws UploadedFileException
     */
    public function save(UploadedFile $uploadedFile): File
    {
        return $uploadedFile->save($this->contentDirectory);
    }
}
