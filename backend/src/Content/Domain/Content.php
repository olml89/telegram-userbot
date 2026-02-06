<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Category\Domain\Category;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\IsEntity;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;
use Symfony\Component\Uid\Uuid;

final class Content implements Entity
{
    use IsEntity;

    private int $sales = 0;

    public function __construct(
        protected readonly Uuid $publicId,
        private string $title,
        private string $description,
        private int $intensity,
        private float $price,
        private Language $language,
        private Mode $mode,
        private Status $status,
        private Category $category,

        /** @var Tag[] */
        private iterable $tags,

        /** @var File[] */
        private iterable $files,
    ) {
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function intensity(): int
    {
        return $this->intensity;
    }

    public function price(): float
    {
        return $this->price;
    }

    public function sales(): int
    {
        return $this->sales;
    }

    public function language(): Language
    {
        return $this->language;
    }

    public function mode(): Mode
    {
        return $this->mode;
    }

    public function status(): Status
    {
        return $this->status;
    }

    public function category(): Category
    {
        return $this->category;
    }

    /**
     * @return iterable<Tag>
     */
    public function tags(): iterable
    {
        return $this->tags;
    }

    /**
     * @return iterable<File>
     */
    public function files(): iterable
    {
        return $this->files;
    }

    public function stored(): self
    {
        return $this->record(new ContentStored($this));
    }
}
