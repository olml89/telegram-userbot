<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Doctrine\Listeners;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;
use olml89\TelegramUserbot\Backend\Content\Domain\Content;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentFile\ContentFile;
use olml89\TelegramUserbot\Backend\Content\Domain\ContentFile\ContentFileManager;
use olml89\TelegramUserbot\Backend\Content\Domain\Tag\TagManager;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\ReadonlyArrayCollection;
use olml89\TelegramUserbot\Backend\Tag\Domain\Tag;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Throwable;

/**
 * Listener that synchronises between the internal representation of Doctrine and the ContentFileManager and TagManager,
 * maintaining the Content domain clean.
 */
#[AsEntityListener(event: Events::postLoad, entity: Content::class)]
#[AsEntityListener(event: Events::preFlush, entity: Content::class)]
#[AsDoctrineListener(event: Events::postFlush)]
final readonly class ContentListener
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * @throws ReflectionException
     */
    private function getProperty(Content $content, string $propertyName): ReflectionProperty
    {
        $contentReflection = new ReflectionClass($content);
        $contentFilesProperty = $contentReflection->getProperty($propertyName);
        $contentFilesProperty->setAccessible(true);

        return $contentFilesProperty;
    }

    /**
     * @template TKey of array-key
     * @template TValue
     *
     * @param ReadonlyArrayCollection<TKey, TValue> $readonlyArrayCollection
     *
     * @return array<TKey, TValue>
     */
    private function getReadonlyArrayCollectionItems(ReadonlyArrayCollection $readonlyArrayCollection): array
    {
        $readonlyArrayCollectionReflection = new ReflectionClass($readonlyArrayCollection);
        $itemsProperty = $readonlyArrayCollectionReflection->getProperty('items');
        $itemsProperty->setAccessible(true);

        /** @var array<TKey, TValue> $items */
        $items = $itemsProperty->getValue($readonlyArrayCollection);

        return $items;
    }

    /**
     * @param PersistentCollection<array-key, mixed> $persistentCollection
     *
     * @return array<array-key, mixed>
     */
    private function getPersistentCollectionElements(PersistentCollection $persistentCollection): array
    {
        $persistentCollectionReflection = new ReflectionClass($persistentCollection);
        $collectionProperty = $persistentCollectionReflection->getProperty('collection');
        $collectionProperty->setAccessible(true);

        /** @var ArrayCollection<array-key, mixed> $arrayCollection */
        $arrayCollection = $collectionProperty->getValue($persistentCollection);

        $arrayCollectionReflection = new ReflectionClass($arrayCollection);
        $elementsProperty = $arrayCollectionReflection->getProperty('elements');
        $elementsProperty->setAccessible(true);

        /** @var array<array-key, mixed> $elements */
        $elements = $elementsProperty->getValue($arrayCollection);

        return $elements;
    }

    /**
     * When creating a new Content, the original PersistentCollections are not initialized yet; we do it here.
     * When updating a Content, we need to recover the original PersistentCollections from the Unit Of Work.
     *
     * @return PersistentCollection<array-key, mixed>
     */
    private function getOriginalOrCreatePersistentCollection(Content $content, ReflectionProperty $property): PersistentCollection
    {
        $uow = $this->entityManager->getUnitOfWork();
        $persistentCollection = $uow->getOriginalEntityData($content)[$property->getName()] ?? null;

        if ($persistentCollection instanceof PersistentCollection) {
            $persistentCollection->initialize();
            $persistentCollection->clear();

            return $persistentCollection;
        }

        return new PersistentCollection(
            $this->entityManager,
            $this->entityManager->getClassMetadata(Content::class),
            new ArrayCollection(),
        );
    }

    /**
     * @param array<array-key, mixed> $items
     */
    private function replaceWithPersistentCollection(Content $content, ReflectionProperty $property, array $items): void
    {
        /**
         * Try to load the original PersistentCollection from the database snapshot.
         * If it doesn't exist, create a new one.
         */
        $persistentCollection = $this->getOriginalOrCreatePersistentCollection(
            $content,
            $property,
        );

        foreach ($items as $key => $item) {
            $persistentCollection->set($key, $item);
        }

        $property->setValue($content, $persistentCollection);
    }

    private function convertToDomainCollections(Content $content): void
    {
        try {
            $contentFilesProperty = $this->getProperty($content, 'contentFiles');
            $contentFiles = $contentFilesProperty->getValue($content);

            if ($contentFiles instanceof PersistentCollection) {
                $contentFiles->initialize();

                /** @var array<int, ContentFile> $elements */
                $elements = $this->getPersistentCollectionElements($contentFiles);

                $contentFilesProperty->setValue(
                    $content,
                    new ContentFileManager(...$elements),
                );
            }

            $tagsProperty = $this->getProperty($content, 'tags');
            $tags = $tagsProperty->getValue($content);

            if ($tags instanceof PersistentCollection) {
                $tags->initialize();

                /** @var array<int, Tag> $elements */
                $elements = $this->getPersistentCollectionElements($tags);

                $tagsProperty->setValue(
                    $content,
                    new TagManager(...$elements),
                );
            }
        } catch (Throwable) {

        }
    }

    private function convertToPersistentCollections(Content $content): void
    {
        try {
            $contentFilesProperty = $this->getProperty($content, 'contentFiles');
            $contentFiles = $contentFilesProperty->getValue($content);

            if ($contentFiles instanceof ContentFileManager) {
                $items = $this->getReadonlyArrayCollectionItems($contentFiles);

                $this->replaceWithPersistentCollection(
                    $content,
                    $contentFilesProperty,
                    $items,
                );
            }

            $tagsProperty = $this->getProperty($content, 'tags');
            $tags = $tagsProperty->getValue($content);

            if ($tags instanceof TagManager) {
                $items = $this->getReadonlyArrayCollectionItems($tags);

                $this->replaceWithPersistentCollection(
                    $content,
                    $tagsProperty,
                    $items,
                );
            }

        } catch (Throwable) {

        }
    }

    /**
     * After loading from the DB: convert to Domain Collections
     */
    public function postLoad(Content $content): void
    {
        $this->convertToDomainCollections($content);
    }

    /**
     * Before any flush (insert or update): convert ContentFileManager and TagManager to PersistentCollections.
     * This is called ONCE per flush, regardless of operation type
     */
    public function preFlush(Content $content): void
    {
        $this->convertToPersistentCollections($content);
    }

    /**
     * postFlush: convert to PersistentCollections to ContentFileManager and TagManager back again.
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        $uow = $args->getObjectManager()->getUnitOfWork();

        foreach ($uow->getIdentityMap() as $entityMap) {
            foreach ($entityMap as $entity) {
                if (!$entity instanceof Content) {
                    continue;
                }

                $this->convertToDomainCollections($entity);
            }
        }
    }
}
