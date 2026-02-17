<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Http\Api\Validate;

use olml89\TelegramUserbot\Backend\File\Application\Validate\ValidateFileCommand;
use Symfony\Component\Validator\Constraints as Validation;
use Webmozart\Assert\Assert;

readonly class ValidateFileRequest
{
    public function __construct(
        #[Validation\NotNull(message: 'The originalName is required')]
        public ?string $originalName,

        #[Validation\NotNull(message: 'The mimeType is required')]
        public ?string $mimeType,

        #[Validation\NotNull(message: 'The size is required')]
        public ?int $size,
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
