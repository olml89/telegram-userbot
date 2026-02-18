<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\Image;

interface ImageSpecializer
{
    /** @throws FileSpecializationException */
    public function specialize(File $file): Image;
}
