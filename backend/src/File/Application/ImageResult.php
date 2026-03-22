<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application;

use olml89\TelegramUserbot\Backend\File\Domain\Image;

final readonly class ImageResult extends FileResult
{
    public int $width;
    public int $height;

    public function __construct(Image $image)
    {
        parent::__construct($image);

        $this->width = $image->resolution()->width;
        $this->height = $image->resolution()->height;
    }
}
