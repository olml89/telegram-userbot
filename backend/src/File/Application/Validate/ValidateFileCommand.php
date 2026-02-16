<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Validate;

final readonly class ValidateFileCommand
{
    public function __construct(
        public string $originalName,
        public string $mimeType,
        public int $size,
    ) {}
}
