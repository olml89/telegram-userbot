<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Application;

use olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile\UploadedFile;

final readonly class UploadContentCommand
{
    public function __construct(
        public string $name,
        public ?string $description,
        public UploadedFile $file,

        /** @var string[] */
        public array $tags,
    ) {
    }
}
