<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\HasCollectionInvariants;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\ReadonlyArrayCollection;

/**
 * @extends ReadonlyArrayCollection<string, UnattachedFile>
 */
final class UnattachedFileCollection extends ReadonlyArrayCollection
{
    use HasCollectionInvariants;

    /**
     * @throws FileCollectionCountException
     */
    public function __construct(UnattachedFile ...$unattachedFiles)
    {
        parent::__construct();

        $this->setInvariant(new FileCollectionCountException());

        foreach ($unattachedFiles as $unattachedFile) {
            $this->checkBeforeInsert();
            $this->items[$unattachedFile->file()->publicId()->toRfc4122()] = $unattachedFile;
        }

        $this->checkInvariants();
    }
}
