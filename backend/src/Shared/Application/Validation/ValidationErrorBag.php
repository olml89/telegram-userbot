<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Application\Validation;

final class ValidationErrorBag
{
    /**
     * @var string[]
     */
    public array $errorMessages = [];

    public function add(string $errorMessage): void
    {
        $this->errorMessages[] = $errorMessage;
    }

    public function formatErrorMessages(): string
    {
        return implode(', ', $this->errorMessages);
    }
}
