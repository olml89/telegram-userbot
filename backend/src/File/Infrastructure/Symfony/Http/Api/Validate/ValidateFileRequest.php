<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Http\Api\Validate;

use olml89\TelegramUserbot\Backend\File\Application\Validate\ValidateFileCommand;
use Symfony\Component\Validator\Constraints as Validation;
use Webmozart\Assert\Assert;

final readonly class ValidateFileRequest
{
    public function __construct(
        /**
         * Use the Choice validation here as event type is related to the tusd infrastructure, not to the domain.
         */
        #[Validation\NotNull(message: 'The eventType is required')]
        #[Validation\Choice(
            callback: [EventType::class, 'values'],
            message: 'The eventType is invalid',
        )]
        public ?string $eventType = null,

        #[Validation\NotNull(message: 'The originalName is required')]
        public ?string $originalName = null,

        #[Validation\NotNull(message: 'The mimeType is required')]
        public ?string $mimeType = null,

        #[Validation\NotNull(message: 'The size is required')]
        public ?int $size = null,
    ) {}

    public function command(): ValidateFileCommand
    {
        Assert::notNull($this->originalName);
        Assert::notNull($this->mimeType);
        Assert::notNull($this->size);

        return new ValidateFileCommand(
            originalName: $this->originalName,
            mimeType: $this->mimeType,
            size: $this->size,
        );
    }
}
