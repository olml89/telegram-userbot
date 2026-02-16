<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

final class FileAlreadyAttachedException extends InvariantException
{
    public function __construct(File $file)
    {
        parent::__construct(sprintf('File %s is already attached', $file->publicId()->toRfc4122()));
    }
}
