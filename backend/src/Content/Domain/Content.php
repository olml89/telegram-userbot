<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\IsEntity;
use Symfony\Component\Uid\Uuid;

final class Content implements Entity
{
    use IsEntity;

    public function __construct(
        protected readonly Uuid $publicId,
        private string $name,
        private ?string $description,
        public readonly File $file,
        /** @var string[] */
        private array $tags,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function file(): File
    {
        return $this->file;
    }

    /**
     * @return string[]
     */
    public function tags(): array
    {
        return $this->tags;
    }
}
