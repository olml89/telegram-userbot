<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\MimeType;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvalidResourceException;

final class InvalidMimeTypeException extends InvalidResourceException
{
    public function __construct(string $mimeType)
    {
        parent::__construct(sprintf('Invalid mime type: %s', $mimeType));
    }
}
