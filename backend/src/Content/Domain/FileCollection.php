<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Domain;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\Shared\Domain\Collection\Collection;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\CollectionCountException;

/**
 * @extends Collection<File>
 */
final class FileCollection extends Collection
{
    private const int MIN_COUNT = 1;
    private const int MAX_COUNT = 10;

    /**
     * @throws CollectionCountException
     */
    public function __construct(File ...$files)
    {
        foreach ($files as $file) {
            $this->add($file);
        }

        if ($this->count() < self::MIN_COUNT) {
            throw new CollectionCountException(self::MIN_COUNT, self::MAX_COUNT);
        }
    }

    /**
     * @throws CollectionCountException
     */
    public function add(File $file): self
    {
        if ($this->count() > self::MAX_COUNT) {
            throw new CollectionCountException(self::MIN_COUNT, self::MAX_COUNT);
        }

        $this->items[] = $file;

        return $this;
    }
}
