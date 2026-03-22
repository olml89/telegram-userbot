<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application;

use olml89\TelegramUserbot\Backend\File\Domain\Video;

final readonly class VideoResult extends FileResult
{
    public float $duration;
    public int $width;
    public int $height;

    public function __construct(Video $video)
    {
        parent::__construct($video);

        $this->duration = $video->duration()->value;
        $this->width = $video->resolution()->width;
        $this->height = $video->resolution()->height;
    }
}
