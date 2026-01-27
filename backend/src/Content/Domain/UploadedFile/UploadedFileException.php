<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile;

use Exception;
use olml89\TelegramUserbot\Backend\Content\Domain\File;
use Throwable;

final class UploadedFileException extends Exception
{
    private function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function errorSaving(File $contentFile, string $directory, ?Throwable $e): self
    {
        return new self(
            message: sprintf(
                'Error saving uploaded file %s to %s',
                $contentFile->originalName,
                $contentFile->path($directory),
            ),
            previous: $e,
        );
    }

    public static function alreadySaved(File $contentFile): self
    {
        return new self(
            message: sprintf(
                'File %s already saved',
                $contentFile->originalName,
            ),
        );
    }
}
