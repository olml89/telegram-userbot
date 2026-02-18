<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Thumbnail;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\NotFoundException;

final class ThumbnailNotFoundException extends NotFoundException
{
    public function __construct(File $file)
    {
        parent::__construct(
            sprintf(
                'Thumbnail does not exist for file with mimeType %s',
                $file->mimeType()->value,
            ),
        );
    }
}
