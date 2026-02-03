<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\NotFoundException;
use Symfony\Component\Uid\Uuid;

final class FileNotFoundException extends NotFoundException
{
    public function __construct(Uuid $publicId)
    {
        parent::__construct("File {$publicId->toRfc4122()} not found.");
    }
}
