<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Collection;

use ArrayAccess;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @extends ReadonlyCollection<TKey, TValue>
 * @extends ArrayAccess<TKey, TValue>
 */
interface Collection extends ReadonlyCollection, ArrayAccess {}
