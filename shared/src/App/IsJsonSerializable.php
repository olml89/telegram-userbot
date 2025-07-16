<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\App;

use JsonSerializable;

/**
 * @mixin JsonSerializable
 */
trait IsJsonSerializable
{
    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            get_object_vars($this),
            fn (mixed $property): bool => !is_null($property),
        );
    }
}
