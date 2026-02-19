<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\StorageFile;

use LogicException;
use RuntimeException;
use SplFileObject;

final class StorageFile extends SplFileObject
{
    public function __construct(string $path)
    {
        try {
            parent::__construct($path);
        } catch (LogicException|RuntimeException $e) {
            throw new StorageFileNotReadableException($path, $e);
        }
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
}
