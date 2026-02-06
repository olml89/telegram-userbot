<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Upload;

use Exception;
use Throwable;

final class UploadInfoException extends Exception
{
    public function __construct(string $path, string $message, ?Throwable $previous = null)
    {
        parent::__construct(
            message: sprintf(
                'Error in %s: %s',
                $path,
                $message,
            ),
            previous: $previous,
        );
    }

    public static function fromMessage(string $path, string $message): self
    {
        return new self($path, $message);
    }

    public static function fromException(string $path, string $message, Throwable $previous): self
    {
        return new self($path, $message, $previous);
    }
}
