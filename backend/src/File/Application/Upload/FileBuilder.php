<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\Upload;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\FileMetadataStrippingException;
use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileName;
use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileNameLengthException;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\FileSpecializationException;
use olml89\TelegramUserbot\Backend\File\Domain\MimeType\MimeType;
use olml89\TelegramUserbot\Backend\File\Domain\MimeType\UnsupportedMimeTypeException;
use olml89\TelegramUserbot\Backend\File\Domain\OriginalName\OriginalName;
use olml89\TelegramUserbot\Backend\File\Domain\OriginalName\OriginalNameLengthException;
use olml89\TelegramUserbot\Backend\File\Domain\Size\Size;
use olml89\TelegramUserbot\Backend\File\Domain\Size\SizeException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\Upload;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumptionException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadFinder;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadNotFoundException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadReadingException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadRemovalException;
use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\UnsupportedResourceException;
use Symfony\Component\Uid\Uuid;

final readonly class FileBuilder
{
    public function __construct(
        private UploadFinder $uploadFinder,
        private FileManager $fileManager,
        private FileProcessor $fileProcessor,
    ) {}

    /**
     * @throws UploadNotFoundException
     * @throws UploadReadingException
     * @throws UnsupportedResourceException
     * @throws ValidationException
     * @throws UploadConsumptionException
     * @throws UploadRemovalException
     * @throws FileSpecializationException
     * @throws FileMetadataStrippingException
     */
    public function build(UploadFileCommand $command): File
    {
        $upload = $this->uploadFinder->find($command->uploadId);
        $file = $this->consumeUpload($upload);

        return $this->fileProcessor->process($file);
    }

    /**
     * @throws UploadReadingException
     * @throws UnsupportedResourceException
     * @throws ValidationException
     * @throws UploadConsumptionException
     * @throws UploadRemovalException
     */
    private function consumeUpload(Upload $upload): File
    {
        $file = $this->buildFile($upload);
        $this->fileManager->consume($file, $upload);

        return $file;
    }

    /**
     * @throws UploadReadingException
     * @throws UnsupportedResourceException
     * @throws ValidationException
     * @throws UploadRemovalException
     */
    private function buildFile(Upload $upload): File
    {
        $validationException = new ValidationException();
        $fileId = Uuid::v4();

        try {
            $mimeType = $this->buildMimeType($upload);
            $fileName = $this->buildFileName($validationException, $fileId, $upload);
            $originalName = $this->buildOriginalName($validationException, $upload);
            $size = $this->buildSize($validationException, $upload);

            if ($validationException->hasErrors()) {
                throw $validationException;
            }

            /**
             * @var FileName $fileName
             * @var OriginalName $originalName
             * @var Size $size
             */
            return new File(
                publicId: $fileId,
                fileName: $fileName,
                originalName: $originalName,
                mimeType: $mimeType,
                bytes: $size,
            );
        } catch (UploadReadingException|UnsupportedResourceException|ValidationException $e) {
            /**
             * Rollback: delete Upload data if there's an error while trying to build File
             */
            $upload->remove();

            throw $e;
        }
    }

    /**
     * @throws UploadReadingException
     * @throws UnsupportedResourceException
     */
    private function buildMimeType(Upload $upload): MimeType
    {
        $mimeType = $upload->mimeType();

        try {
            return MimeType::create($mimeType);
        } catch (UnsupportedMimeTypeException $e) {
            throw new UnsupportedResourceException($e);
        }
    }

    /**
     * @throws UploadReadingException
     */
    private function buildFileName(ValidationException $validationException, Uuid $fileId, Upload $upload): ?FileName
    {
        try {
            return FileName::from(
                name: $fileId,
                extension: $upload->extension(),
            );
        } catch (FileNameLengthException $e) {
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
        } catch (OriginalNameLengthException $e) {
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
