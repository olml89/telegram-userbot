<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application;

use olml89\TelegramUserbot\Backend\File\Domain\Audio;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\Image;
use olml89\TelegramUserbot\Backend\File\Domain\Video;

final readonly class FileResultFactory
{
    public function create(File $file): FileResult|ImageResult|AudioResult|VideoResult
    {
        return match (true) {
            $file instanceof Image => ImageResult::image($file),
            $file instanceof Audio => AudioResult::audio($file),
            $file instanceof Video => VideoResult::video($file),
            default => FileResult::file($file),
        };
    }
}
