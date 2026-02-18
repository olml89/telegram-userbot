<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Thumbnail;

use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileName;

interface ThumbnailDisplayer
{
    public function thumbnail(): FileName;
}
