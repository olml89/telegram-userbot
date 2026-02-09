<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Application\Validation;

use Exception;

final class ValidationException extends Exception
{
    /**
     * @var array<string, ValidationErrorBag>
     */
    private array $errors = [];

    public function __construct()
    {
        parent::__construct('Validation failed.');
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    public function addError(string $field, string $errorMessage): self
    {
        $this->errors[$field] ??= new ValidationErrorBag();
        $this->errors[$field]->add($errorMessage);

        return $this;
    }

    /**
     * @return ValidationErrorBag[]
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
