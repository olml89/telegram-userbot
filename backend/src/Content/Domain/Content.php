<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use olml89\TelegramUserbot\Backend\Category\Domain\Category;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentFile\ContentFile;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentFile\ContentFileManager;
use olml89\TelegramUserbot\Backend\Content\Domain\Description\Description;
use olml89\TelegramUserbot\Backend\Content\Domain\Language\Language;
use olml89\TelegramUserbot\Backend\Content\Domain\Mode\Mode;
use olml89\TelegramUserbot\Backend\Content\Domain\Price\Price;
use olml89\TelegramUserbot\Backend\Content\Domain\Status\Status;
use olml89\TelegramUserbot\Backend\Content\Domain\Tag\TagManager;
use olml89\TelegramUserbot\Backend\Content\Domain\Title\Title;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotFoundException;
use olml89\TelegramUserbot\Backend\File\Domain\UnattachedFile;
use olml89\TelegramUserbot\Backend\File\Domain\UnattachedFileCollection;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\ReadonlyArrayCollection;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\ReadonlyCollection;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\EventSource\EventSource;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\EventSource\HasEvents;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\HasIdentity;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Timestampable\HasTimestamps;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Timestampable\Timestampable;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\CollectionCountException;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Percentage\Percentage;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Timestamps\Timestamps;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagCollection;
use olml89\TelegramUserbot\Backend\Tag\Domain\TagCollectionCountException;
use Symfony\Component\Uid\Uuid;

final class Content implements Entity, EventSource, Timestampable
{
    use HasIdentity;
    use HasEvents;
    use HasTimestamps;

    private int $sales = 0;

    /**
     * @var TagManager $tags
     */
    private ArrayAccess&Countable&IteratorAggregate $tags;

    /**
     * @var ContentFileManager $contentFiles
     */
    private ArrayAccess&Countable&IteratorAggregate $contentFiles;

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
        UnattachedFileCollection $unattachedFiles,
        protected readonly Timestamps $timestamps = new Timestamps(),
    ) {
        $this->tags = new TagManager(...$tags);

        $contentFiles = $unattachedFiles->map(
            fn(UnattachedFile $unattachedFile): ContentFile => $this->attachFile($unattachedFile),
        );

        $this->contentFiles = new ContentFileManager(...$contentFiles);
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
     * @return ReadonlyCollection<int, Tag>
     */
    public function tags(): ReadonlyCollection
    {
        return new ReadonlyArrayCollection(iterator_to_array($this->tags, preserve_keys: false));
    }

    /**
     * @throws TagCollectionCountException
     */
    public function addTag(Tag $tag): Tag
    {
        return $this->tags->add($tag);
    }

    /**
     * @return ReadonlyCollection<int, ContentFile>
     */
    public function contentFiles(): ReadonlyCollection
    {
        return new ReadonlyArrayCollection(iterator_to_array($this->contentFiles, preserve_keys: false));
    }

    /**
     * @throws CollectionCountException
     */
    public function addFile(UnattachedFile $unattachedFile): ContentFile
    {
        $contentFile = $this->attachFile($unattachedFile);

        return $this->contentFiles->add($contentFile);
    }

    private function attachFile(UnattachedFile $unattachedFile): ContentFile
    {
        $contentFile = $unattachedFile->attach($this)->attached();
        $this->record(...$contentFile->pullEvents());

        return $contentFile;
    }

    /**
     * @throws CollectionCountException
     * @throws FileNotFoundException
     */
    public function removeFile(Uuid $fileId): UnattachedFile
    {
        $contentFile = $this->contentFiles->remove($fileId)->removed();
        $this->record(...$contentFile->pullEvents());

        return $contentFile->detach();
    }

    public function removed(): self
    {
        return $this->record(new ContentRemoved($this));
    }

    public function stored(): self
    {
        return $this->record(new ContentStored($this));
    }
}
