<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Enum;

/**
 * @mixin SerializableStringBackedEnum
 */
trait IsSerializableStringBackedEnum
{
    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label(),
        ];
    }
}
