<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\StorageFile;

use RuntimeException;
use SplFileInfo;

final class StorageFile extends SplFileInfo
{
    public function assertExists(): self
    {
        if (!is_file($this->getPathname())) {
            throw new StorageFileNotReadableException($this);
        }

        return $this;
    }

    /**
     * @throws StorageFileSizeException
     */
    public function getSize(): int
    {
        try {
            if (($size = parent::getSize()) === false) {
                throw new StorageFileSizeException($this);
            }

            return $size;
        } catch (RuntimeException $e) {
            throw new StorageFileSizeException($this, $e);
        }
    }

    public function move(self $to): self
    {
        rename($this->getPathname(), $to->getPathname());

        return new self($to->getPathname());
    }
}
