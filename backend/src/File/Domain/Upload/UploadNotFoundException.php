<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Upload;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\NotFoundException;
use Throwable;

final class UploadNotFoundException extends NotFoundException
{
    public function __construct(string $uploadDirectory, string $uploadId, Throwable $previous)
    {
        parent::__construct(
            message: sprintf('Upload %s not found in %s', $uploadId, $uploadDirectory),
            previous: $previous,
        );
    }
}
