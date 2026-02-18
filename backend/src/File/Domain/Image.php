<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\File\Domain\Resolution\Resolution;

final class Image extends File
{
    public function __construct(
        File $file,
        private readonly Resolution $resolution,
    ) {
        parent::__construct(
            $file->publicId(),
            $file->name(),
            $file->originalName(),
            $file->mimeType(),
            $file->bytes(),
        );
    }

    public function resolution(): Resolution
    {
        return $this->resolution;
    }
}
