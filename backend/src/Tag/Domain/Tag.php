<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\IsEntity;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name\Name;
use Symfony\Component\Uid\Uuid;

final class Tag implements Entity
{
    use IsEntity;

    public function __construct(
        protected readonly Uuid $publicId,
        private Name $name,
    ) {
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function stored(): self
    {
        return $this->record(new TagStored($this));
    }
}
