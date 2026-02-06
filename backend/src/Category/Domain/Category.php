<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Category\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\IsEntity;
use Symfony\Component\Uid\Uuid;

final class Category implements Entity
{
    use IsEntity;

    public function __construct(
        protected readonly Uuid $publicId,
        private readonly string $name,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }
}
