<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Entity;

use LogicException;
use Symfony\Component\Uid\Uuid;

/**
 * @mixin Entity
 */
trait IsEntity
{
    protected ?int $id = null;
    protected readonly Uuid $publicId;

    /**
     * @var Event[]
     */
    private array $events = [];

    public function id(): int
    {
        if (is_null($this->id)) {
            throw new LogicException(sprintf('%s: id has not been set yet', $this::class));
        }

        return $this->id;
    }

    public function publicId(): Uuid
    {
        return $this->publicId;
    }

    public function record(Event $event): static
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * @return Event[]
     */
    public function events(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
