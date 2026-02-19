<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\EventSource\EventSource;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\EventSource\HasEvents;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\HasIdentity;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name\Name;
use Symfony\Component\Uid\Uuid;

final class Tag implements EventSource
{
    use HasIdentity;
    use HasEvents;

    public function __construct(
        protected readonly Uuid $publicId,
        private Name $name,
    ) {}

    public function name(): Name
    {
        return $this->name;
    }

    public function stored(): self
    {
        return $this->record(new TagStored($this));
    }
}
