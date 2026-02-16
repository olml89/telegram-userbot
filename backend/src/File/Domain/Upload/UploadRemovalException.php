<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Upload;

use Exception;
use Throwable;

final class UploadRemovalException extends Exception
{
    public function __construct(string $path, ?Throwable $previous)
    {
        parent::__construct(
            message: sprintf(
                'Error removing %s',
                $path,
            ),
            previous: $previous,
        );
    }
}
