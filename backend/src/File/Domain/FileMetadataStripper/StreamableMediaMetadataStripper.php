<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper;

use olml89\TelegramUserbot\Backend\File\Domain\Audio;
use olml89\TelegramUserbot\Backend\File\Domain\Video;

interface
StreamableMediaMetadataStripper
{
    /** @throws FileMetadataStrippingException */
    public function strip(Audio|Video $streamableMedia): true;
}
