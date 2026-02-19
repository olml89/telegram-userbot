<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper;

use olml89\TelegramUserbot\Backend\File\Domain\Image;

interface ImageMetadataStripper
{
    /** @throws FileMetadataStrippingException */
    public function strip(Image $image): true;
}
