<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Category\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\NotFoundException;
use Symfony\Component\Uid\Uuid;

final class CategoryNotFoundException extends NotFoundException
{
    public function __construct(Uuid $publicId)
    {
        parent::__construct(sprintf('Category %s not found', $publicId->toRfc4122()));
    }
}
