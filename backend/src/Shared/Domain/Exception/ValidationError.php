<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception;

final readonly class ValidationError
{
    public function __construct(
        public string $field,
        public string $errorMessage,
    ) {
    }
}
