<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Upload;

use olml89\TelegramUserbot\Backend\File\Domain\File;

interface Upload
{
    public function id(): string;
    public function originalName(): string;
    public function extension(): string;
    public function mimeType(): string;
    public function bytes(): int;

    /** @throws UploadConsumptionException */
    public function move(string $destinationDirectory, File $file): void;
}
