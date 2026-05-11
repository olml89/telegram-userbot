<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\NotAllowedException;

final class FileAlreadyAttachedException extends NotAllowedException
{
    public function __construct(File $file)
    {
        parent::__construct(sprintf('File %s is already attached', $file->publicId()->toRfc4122()));
    }
}
