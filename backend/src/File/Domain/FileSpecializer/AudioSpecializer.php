<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer;

use olml89\TelegramUserbot\Backend\File\Domain\Audio;
use olml89\TelegramUserbot\Backend\File\Domain\File;

interface AudioSpecializer
{
    /** @throws FileSpecializationException */
    public function specialize(File $file): Audio;
}
