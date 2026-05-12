<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use Symfony\Component\Uid\Uuid;

interface UnattachedFileRepository
{
    /** @throws FileAlreadyAttachedException */
    public function get(Uuid $publicId): ?UnattachedFile;
    public function remove(UnattachedFile $unattachedFile): void;
    public function store(UnattachedFile $unattachedFile): void;
}
