<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception;

use Exception;
use olml89\TelegramUserbot\Backend\Shared\Domain\Entity\Entity;

final class ValidationException extends Exception
{
    private Entity $entity;

    /**
     * @var ValidationError[]
     */
    private array $errors;

    public function __construct(Entity $entity, ValidationError ...$errors)
    {
        $this->entity = $entity;
        $this->errors = $errors;

        parent::__construct('Validation failed.');
    }

    public function entity(): Entity
    {
        return $this->entity;
    }

    /**
     * @return ValidationError[]
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
