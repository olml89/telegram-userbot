<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Upload;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\MimeType\UnsupportedMimeTypeException;
use olml89\TelegramUserbot\Backend\File\Domain\MimeType\MimeType;
use olml89\TelegramUserbot\Backend\File\Domain\OriginalName;
use olml89\TelegramUserbot\Backend\File\Domain\Size\Size;
use olml89\TelegramUserbot\Backend\File\Domain\Size\SizeException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\Upload;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumptionException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadFinder;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadReadingException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadNotFoundException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadRemovalException;
use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\StringLengthException;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name\Name;
use Symfony\Component\Uid\Uuid;

final readonly class FileBuilder
{
    public function __construct(
        private UploadFinder $uploadFinder,
        private FileManager $fileManager,
    ) {}

    /**
     * @throws UploadNotFoundException
     * @throws UploadReadingException
     * @throws UnsupportedMimeTypeException
     * @throws ValidationException
     * @throws UploadConsumptionException
     * @throws UploadRemovalException
     */
    public function build(UploadFileCommand $command): File
    {
        $upload = $this->uploadFinder->find($command->uploadId);

        try {
            $file = $this->buildFile($upload);
            $this->fileManager->consume($file, $upload);

            return $file;
        } catch (UploadReadingException|UnsupportedMimeTypeException|ValidationException $e) {
            /**
             * Rollback: delete Upload data if there's an error while trying to build File
             */
            $upload->remove();

            throw $e;
        }
    }

    /**
     * @throws UploadReadingException
     * @throws UnsupportedMimeTypeException
     * @throws ValidationException
     */
    private function buildFile(Upload $upload): File
    {
        $mimeType = $this->buildMimeType($upload);

        $validationException = new ValidationException();
        $fileId = Uuid::v4();
        $name = $this->buildName($validationException, $fileId, $upload);
        $originalName = $this->buildOriginalName($validationException, $upload);
        $size = $this->buildSize($validationException, $upload);

        if ($validationException->hasErrors()) {
            throw $validationException;
        }

        /**
         * @var Name $name
         * @var OriginalName $originalName
         * @var Size $size
         */
        return new File(
            publicId: $fileId,
            name: $name,
            originalName: $originalName,
            mimeType: $mimeType,
            bytes: $size,
        );
    }

    /**
     * @throws UploadReadingException
     * @throws UnsupportedMimeTypeException
     */
    private function buildMimeType(Upload $upload): MimeType
    {
        $mimeType = $upload->mimeType();

        return MimeType::tryFrom($mimeType) ?? throw new UnsupportedMimeTypeException($mimeType);
    }

    /**
     * @throws UploadReadingException
     */
    private function buildName(ValidationException $validationException, Uuid $fileId, Upload $upload): ?Name
    {
        try {
            return new Name(
                sprintf(
                    '%s.%s',
                    $fileId->toRfc4122(),
                    $upload->extension(),
                ),
            );
        } catch (StringLengthException $e) {
            $validationException->addError('name', $e->getMessage());

            return null;
        }
    }

    /**
     * @throws UploadReadingException
     */
    private function buildOriginalName(ValidationException $validationException, Upload $upload): ?OriginalName
    {
        try {
            return new OriginalName($upload->originalName());
        } catch (StringLengthException $e) {
            $validationException->addError('originalName', $e->getMessage());

            return null;
        }
    }

    /**
     * @throws UploadReadingException
     */
    private function buildSize(ValidationException $validationException, Upload $upload): ?Size
    {
        try {
            return new Size($upload->bytes());
        } catch (SizeException $e) {
            $validationException->addError('size', $e->getMessage());

            return null;
        }
    }
}
