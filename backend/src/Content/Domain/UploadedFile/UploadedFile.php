<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile;

use olml89\TelegramUserbot\Backend\Content\Domain\File;

interface UploadedFile
{
    public function name(): string;
    public function originalName(): string;
    public function mimeType(): string;
    public function size(): int;


    /** @throws UploadedFileException */
    public function save(string $contentDirectory): File;
}
