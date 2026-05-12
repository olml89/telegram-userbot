<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer;

use olml89\TelegramUserbot\Backend\File\Domain\Image;
use olml89\TelegramUserbot\Backend\File\Domain\UnattachedFile;

interface ImageSpecializer
{
    /** @throws FileSpecializationException */
    public function specialize(UnattachedFile $unattachedFile): Image;
}
