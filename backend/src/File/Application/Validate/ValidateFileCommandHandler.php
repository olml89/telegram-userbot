<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Validate;

use olml89\TelegramUserbot\Backend\File\Domain\MimeType\MimeType;
use olml89\TelegramUserbot\Backend\File\Domain\MimeType\UnsupportedMimeTypeException;
use olml89\TelegramUserbot\Backend\File\Domain\OriginalName\OriginalName;
use olml89\TelegramUserbot\Backend\File\Domain\OriginalName\OriginalNameLengthException;
use olml89\TelegramUserbot\Backend\File\Domain\Size\Size;
use olml89\TelegramUserbot\Backend\File\Domain\Size\SizeException;
use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;

final readonly class ValidateFileCommandHandler
{
    /**
     * @throws ValidationException
     */
    public function handle(ValidateFileCommand $command): void
    {
        $validationException = new ValidationException();
        $this->buildOriginalName($validationException, $command);
        $this->buildMimeType($validationException, $command);
        $this->buildSize($validationException, $command);

        if ($validationException->hasErrors()) {
            throw $validationException;
        }
    }

    private function buildMimeType(ValidationException $validationException, ValidateFileCommand $command): void
    {
        try {
            MimeType::create($command->mimeType);
        } catch (UnsupportedMimeTypeException $e) {
            $validationException->addError('mimeType', $e->getMessage());
        }
    }

    private function buildOriginalName(ValidationException $validationException, ValidateFileCommand $command): void
    {
        try {
            new OriginalName($command->originalName);
        } catch (OriginalNameLengthException $e) {
            $validationException->addError('originalName', $e->getMessage());
        }
    }

    private function buildSize(ValidationException $validationException, ValidateFileCommand $command): void
    {
        try {
            new Size($command->size);
        } catch (SizeException $e) {
            $validationException->addError('size', $e->getMessage());
        }
    }
}
