<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

interface FileManager
{
    public function exists(File $file): bool;
    public function remove(File $file): void;
}
