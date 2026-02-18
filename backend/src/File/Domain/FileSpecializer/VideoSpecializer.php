<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\Video;

interface VideoSpecializer
{
    /** @throws FileSpecializationException */
    public function specialize(File $file): Video;
}
