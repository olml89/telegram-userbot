<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper;

use olml89\TelegramUserbot\Backend\File\Domain\File;

interface ImageMetadataStripper
{
    /** @throws FileMetadataStrippingException */
    public function strip(File $file): void;
}
