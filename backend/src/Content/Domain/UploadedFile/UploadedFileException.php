<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile;

use Exception;
use Throwable;

final class UploadedFileException extends Exception
{
    private function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function errorSaving(UploadedFile $uploadedFile, string $path, ?Throwable $e): self
    {
        return new self(
            message: sprintf(
                'Error saving uploaded file %s to %s',
                $uploadedFile->originalName(),
                $path,
            ),
            previous: $e,
        );
    }

    public static function alreadySaved(UploadedFile $uploadedFile): self
    {
        return new self(
            message: sprintf(
                'File %s already saved',
                $uploadedFile->originalName(),
            ),
        );
    }
}
