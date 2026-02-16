<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Bot;

final readonly class BotSession
{
    public function __construct(
        private string $path,
    ) {}

    public function path(): string
    {
        return $this->path;
    }

    public function reset(): void
    {
        if (! file_exists($this->path)) {
            return;
        }

        $fileNames = array_diff(
            scandir($this->path),
            ['.', '..'],
        );

        foreach ($fileNames as $fileName) {
            $filePath = sprintf('%s/%s', $this->path, $fileName);
            unlink($filePath);
        }

        rmdir($this->path);
        mkdir($this->path, recursive: true);
    }
}
