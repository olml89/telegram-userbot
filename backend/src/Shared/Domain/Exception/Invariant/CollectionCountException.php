<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant;

use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

abstract class CollectionCountException extends InvariantException
{
    public function __construct(int $minCount, int $maxCount)
    {
        parent::__construct(
            sprintf(
                'This collection should contain between %d and %d %s',
                $minCount,
                $maxCount,
                static::elementsName(),
            ),
        );
    }

    abstract protected static function elementsName(): string;
}
