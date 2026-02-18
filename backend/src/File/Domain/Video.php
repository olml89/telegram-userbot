<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\File\Domain\Duration\Duration;
use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileName;
use olml89\TelegramUserbot\Backend\File\Domain\Resolution\Resolution;

final class Video extends File implements ThumbnailDisplayer
{
    public function __construct(
        File $file,
        private readonly FileName $thumbnail,
        private readonly Duration $duration,
        private readonly Resolution $resolution,
    ) {
        parent::__construct(
            $file->publicId(),
            $file->fileName(),
            $file->originalName(),
            $file->mimeType(),
            $file->bytes(),
        );
    }

    public function thumbnail(): FileName
    {
        return $this->thumbnail;
    }

    public function duration(): Duration
    {
        return $this->duration;
    }

    public function resolution(): Resolution
    {
        return $this->resolution;
    }
}
