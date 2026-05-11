<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\File\Domain\Duration\Duration;

final class Audio extends File
{
    public function __construct(
        UnattachedFile $unattachedFile,
        private readonly Duration $duration,
    ) {
        parent::__construct(
            $unattachedFile->file()->publicId(),
            $unattachedFile->file()->fileName(),
            $unattachedFile->file()->originalName(),
            $unattachedFile->file()->mimeType(),
            $unattachedFile->file()->bytes(),
        );
    }

    public function duration(): Duration
    {
        return $this->duration;
    }
}
