<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer;

use Exception;
use Throwable;

final class FileSpecializationException extends Exception
{
    public function __construct(Throwable $previous)
    {
        parent::__construct(
            message: 'Error specializing file',
            previous: $previous,
        );
    }
}
