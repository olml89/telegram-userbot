<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use RuntimeException;

final class FileNotReadableException extends RuntimeException
{
    public function __construct(string $path, \Throwable $previous)
    {
        parent::__construct(
            message: sprintf(
                'File %s is not readable',
                $path,
            ),
            previous: $previous,
        );
    }
}
