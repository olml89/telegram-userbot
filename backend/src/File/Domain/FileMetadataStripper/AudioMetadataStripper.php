<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper;

use olml89\TelegramUserbot\Backend\File\Domain\Audio;

interface AudioMetadataStripper
{
    /** @throws FileMetadataStrippingException */
    public function strip(Audio $file): bool;
}
