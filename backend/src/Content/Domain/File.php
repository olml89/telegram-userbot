<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

final readonly class File
{
    public function __construct(
        public string $name,
        public string $originalName,
        public string $mimeType,
        public int $size,
    ) {
    }

    public function path(string $directory): string
    {
        return sprintf('%s/%s', $directory, $this->name);
    }
}
