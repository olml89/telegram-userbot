<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\CollectionCountException;

final class FileCollectionCountException extends CollectionCountException
{
    private const int MIN_COUNT = 1;
    private const int MAX_COUNT = 10;

    public function __construct()
    {
        parent::__construct(self::MIN_COUNT, self::MAX_COUNT);
    }

    protected static function elementsName(): string
    {
        return 'files';
    }
}
