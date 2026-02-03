<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Application;

/**
 * @mixin Result
 */
trait IsResult
{
    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
