<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Http\Api\Validate;

use Symfony\Component\Validator\Constraints as Validation;

final readonly class TusdValidateFileRequest extends ValidateFileRequest
{
    public function __construct(
        /**
         * Use the Choice validation here as an event type is related to the tusd infrastructure, not to the domain.
         */
        #[Validation\NotNull(message: 'The eventType is required')]
        #[Validation\Choice(
            callback: [EventType::class, 'values'],
            message: 'The eventType is invalid',
        )]
        public ?string $eventType,

        ?string $originalName,
        ?string $mimeType,
        ?int $size,
    ) {
        parent::__construct($originalName, $mimeType, $size);
    }
}
