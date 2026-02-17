<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\File\Domain\Duration\Duration;
use olml89\TelegramUserbot\Backend\File\Domain\MimeType\MimeType;
use olml89\TelegramUserbot\Backend\File\Domain\OriginalName\OriginalName;
use olml89\TelegramUserbot\Backend\File\Domain\Size\Size;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name\Name;
use Symfony\Component\Uid\Uuid;

final class Audio extends File
{
    public function __construct(
        Uuid $publicId,
        Name $name,
        OriginalName $originalName,
        MimeType $mimeType,
        Size $bytes,
        private readonly Duration $duration,
    ) {
        parent::__construct($publicId, $name, $originalName, $mimeType, $bytes);
    }

    public function duration(): Duration
    {
        return $this->duration;
    }
}
