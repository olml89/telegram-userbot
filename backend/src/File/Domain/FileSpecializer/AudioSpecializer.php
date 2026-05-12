<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer;

use olml89\TelegramUserbot\Backend\File\Domain\Audio;
use olml89\TelegramUserbot\Backend\File\Domain\UnattachedFile;

interface AudioSpecializer
{
    /** @throws FileSpecializationException */
    public function specialize(UnattachedFile $unattachedFile): Audio;
}
