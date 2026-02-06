<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Http\Api\Upload;

use olml89\TelegramUserbot\Backend\File\Application\Upload\UploadFileCommand;
use Symfony\Component\Validator\Constraints as Validation;
use Webmozart\Assert\Assert;

final readonly class UploadFileRequest
{
    public function __construct(
        #[Validation\NotBlank]
        public ?string $uploadId,
    ) {
    }

    public function command(): UploadFileCommand
    {
        Assert::notNull($this->uploadId);

        return new UploadFileCommand($this->uploadId);
    }
}
