<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use Symfony\Component\Uid\Uuid;

interface FileRepository
{
    public function get(Uuid $publicId): ?File;
    public function remove(File $file): void;
    public function store(File $file): void;
}
