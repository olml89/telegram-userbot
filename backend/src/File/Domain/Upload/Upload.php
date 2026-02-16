<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Upload;

use olml89\TelegramUserbot\Backend\File\Domain\File;

interface Upload
{
    public function id(): string;

    /** @throws UploadReadingException */
    public function originalName(): string;

    /** @throws UploadReadingException */
    public function extension(): string;

    /** @throws UploadReadingException */
    public function mimeType(): string;

    /** @throws UploadReadingException */
    public function bytes(): int;

    /** @throws UploadConsumptionException */
    public function move(string $destinationDirectory, File $file): void;

    /** @throws UploadRemovalException */
    public function remove(): void;
}
