<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application;

use olml89\TelegramUserbot\Backend\File\Domain\Audio;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\IsResult;
use olml89\TelegramUserbot\Backend\Shared\Application\Result\Result;

final readonly class AudioResult implements Result
{
    use IsResult;

    public function __construct(
        public string $publicId,
        public string $fileName,
        public string $originalName,
        public string $mimeType,
        public int $bytes,
        public float $duration,
    ) {}

    public static function audio(Audio $audio): self
    {
        $fileResult = FileResult::file($audio);

        return new self(
            publicId: $fileResult->publicId,
            fileName: $fileResult->fileName,
            originalName: $fileResult->originalName,
            mimeType: $fileResult->mimeType,
            bytes: $fileResult->bytes,
            duration: $audio->duration()->value,
        );
    }
}
