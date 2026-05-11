<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer;

use olml89\TelegramUserbot\Backend\File\Domain\UnattachedFile;
use olml89\TelegramUserbot\Backend\File\Domain\Video;

interface VideoSpecializer
{
    /** @throws FileSpecializationException */
    public function specialize(UnattachedFile $unattachedFile): Video;
}
