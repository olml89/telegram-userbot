<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\Category\Domain\Category;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileAlreadyAttachedException;
use olml89\TelegramUserbot\Backend\File\Domain\FileAttached;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\Collection;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\GenericCollection;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\IsEntity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\CollectionCountException;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Percentage\Percentage;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;
use Symfony\Component\Uid\Uuid;

final class Content implements Entity
{
    use IsEntity;

    private int $sales = 0;

    /**
     * @var iterable<Tag>
     */
    private iterable $tags;

    /**
     * @var iterable<File>
     */
    private iterable $files;

    public function __construct(
        protected readonly Uuid $publicId,
        private Title $title,
        private Description $description,
        private Percentage $intensity,
        private Price $price,
        private Language $language,
        private Mode $mode,
        private Status $status,
        private Category $category,
        TagCollection $tags,
        FileCollection $files,
    ) {
        $this->tags = $tags->toArray();

        $this->files = $files
            ->each(fn (File $file) => $this->attachFile($file))
            ->toArray();
    }

    public function title(): Title
    {
        return $this->title;
    }

    public function description(): Description
    {
        return $this->description;
    }

    public function intensity(): Percentage
    {
        return $this->intensity;
    }

    public function price(): Price
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
     * @return Collection<Tag>
     */
    public function tags(): Collection
    {
        return new GenericCollection(...$this->tags);
    }

    /**
     * @throws CollectionCountException
     */
    public function addTag(Tag $tag): self
    {
        $this->tags = new TagCollection(...$this->tags)->add($tag)->toArray();

        return $this;
    }

    /**
     * @return Collection<File>
     */
    public function files(): Collection
    {
        return new GenericCollection(...$this->files);
    }

    /**
     * @throws CollectionCountException
     * @throws FileAlreadyAttachedException
     */
    public function addFile(File $file): self
    {
        $this->files = new FileCollection(...$this->files)->add($file)->toArray();
        $this->attachFile($file);

        return $this;
    }

    /**
     * @throws FileAlreadyAttachedException
     */
    private function attachFile(File $file): void
    {
        $file->attach($this);
        $this->record(new FileAttached($this, $file));
    }

    public function stored(): self
    {
        return $this->record(new ContentStored($this));
    }
}
