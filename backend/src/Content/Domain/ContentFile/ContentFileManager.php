<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain\ContentFile;

use olml89\TelegramUserbot\Backend\File\Domain\FileCollectionCountException;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotFoundException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\Collection;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\HasArrayAccess;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\HasCollectionInvariants;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\ReadonlyArrayCollection;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ReadonlyArrayCollection<string, ContentFile>
 * @implements Collection<string, ContentFile>
 */
final class ContentFileManager extends ReadonlyArrayCollection implements Collection
{
    use HasCollectionInvariants;
    use HasArrayAccess;

    /**
     * @throws FileCollectionCountException
     */
    public function __construct(ContentFile ...$contentFiles)
    {
        parent::__construct();

        $this->setInvariant(new FileCollectionCountException());

        foreach ($contentFiles as $contentFile) {
            $this->add($contentFile);
        }

        $this->checkInvariants();
    }

    /**
     * @throws FileCollectionCountException
     */
    public function add(ContentFile $contentFile): ContentFile
    {
        $this->checkBeforeInsert();

        /**
         * The identity is the public id of the File, ContentFile is only a wrapper around the File.
         */
        $this->items[$contentFile->file()->publicId()->toRfc4122()] = $contentFile;

        return $contentFile;
    }

    /**
     * @throws FileCollectionCountException
     * @throws FileNotFoundException
     */
    public function remove(Uuid $fileId): ContentFile
    {
        $this->checkBeforeDelete();

        /**
         * The identity is the public id of the File, ContentFile is only a wrapper around the File.
         */
        $contentFile = $this->items[$fileId->toRfc4122()] ?? throw new FileNotFoundException($fileId);
        unset($this->items[$fileId->toRfc4122()]);

        return $contentFile;
    }
}
