<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper;

use olml89\TelegramUserbot\Backend\File\Domain\Video;

interface VideoMetadataStripper
{
    /** @throws FileMetadataStrippingException */
    public function strip(Video $file): bool;
}
