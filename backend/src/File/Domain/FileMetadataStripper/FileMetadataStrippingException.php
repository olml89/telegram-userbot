<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper;

use Exception;
use Throwable;

final class FileMetadataStrippingException extends Exception
{
    public function __construct(Throwable $previous)
    {
        parent::__construct(
            message: 'Error stripping metadata from file',
            previous: $previous,
        );
    }
}
