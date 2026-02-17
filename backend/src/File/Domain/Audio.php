<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\File\Domain\Duration\Duration;

final class Audio extends File
{
    public function __construct(
        File $file,
        private readonly Duration $duration,
    ) {
        parent::__construct(
            $file->publicId(),
            $file->name(),
            $file->originalName(),
            $file->mimeType(),
            $file->bytes(),
        );
    }

    public function duration(): Duration
    {
        return $this->duration;
    }
}
