<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileName;
use olml89\TelegramUserbot\Backend\File\Domain\Resolution\Resolution;
use olml89\TelegramUserbot\Backend\File\Domain\Thumbnail\ThumbnailDisplayer;

final class Image extends File implements ThumbnailDisplayer
{
    public function __construct(
        UnattachedFile $unattachedFile,
        private readonly Resolution $resolution,
    ) {
        parent::__construct(
            $unattachedFile->file()->publicId(),
            $unattachedFile->file()->fileName(),
            $unattachedFile->file()->originalName(),
            $unattachedFile->file()->mimeType(),
            $unattachedFile->file()->bytes(),
        );
    }

    public function thumbnail(): FileName
    {
        return $this->fileName();
    }

    public function resolution(): Resolution
    {
        return $this->resolution;
    }
}
