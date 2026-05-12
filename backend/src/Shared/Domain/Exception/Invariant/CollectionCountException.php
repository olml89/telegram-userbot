<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant;

use InvalidArgumentException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvariantException;

abstract class CollectionCountException extends InvariantException
{
    private readonly ?int $minCount;
    private readonly ?int $maxCount;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(?int $minCount = null, ?int $maxCount = null)
    {
        $this->minCount = $minCount;
        $this->maxCount = $maxCount;

        parent::__construct($this->createExceptionMessage());
    }

    /**
     * @throws InvalidArgumentException
     */
    private function createExceptionMessage(): string
    {
        if (is_null($this->minCount) && is_null($this->maxCount)) {
            throw new InvalidArgumentException('At least one of the minCount or maxCount must be provided');
        }

        if (!is_null($this->minCount) && !is_null($this->maxCount)) {
            return sprintf(
                'This collection should contain at least %d and at most %d %s',
                $this->minCount,
                $this->maxCount,
                static::elementsName(),
            );
        }

        if (!is_null($this->minCount)) {
            return sprintf(
                'This collection should contain at least %d %s',
                $this->minCount,
                static::elementsName(),
            );
        }

        return sprintf(
            'This collection should contain at most %d %s',
            $this->maxCount,
            static::elementsName(),
        );
    }

    abstract protected static function elementsName(): string;

    public function assertNotLessThanMin(int $count): void
    {
        if (!is_null($this->minCount) && $count < $this->minCount) {
            throw $this;
        }
    }

    public function assertNotGreaterThanMax(int $count): void
    {
        if (!is_null($this->maxCount) && $count > $this->maxCount) {
            throw $this;
        }
    }
}
