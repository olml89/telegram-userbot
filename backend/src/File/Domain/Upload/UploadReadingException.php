<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Upload;

use Exception;
use Throwable;

final class UploadReadingException extends Exception
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
}
