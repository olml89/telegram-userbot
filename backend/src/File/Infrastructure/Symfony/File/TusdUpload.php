<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\File;

use LogicException;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\Upload;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumptionException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadReadingException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadNotFoundException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadRemovalException;
use RuntimeException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Throwable;
use Webmozart\Assert\Assert;

final readonly class TusdUpload implements Upload
{
    private FilesystemFile $upload;
    private FilesystemFile $info;

    /**
     * @throws UploadNotFoundException
     */
    public function __construct(string $uploadDirectory, string $uploadId)
    {
        try {
            $this->upload = FilesystemFile::from(
                $uploadDirectory,
                $uploadId,
            );
            $this->info = FilesystemFile::from(
                $uploadDirectory,
                sprintf('%s.info', $uploadId),
            );
        } catch (FileNotFoundException $e) {
            throw new UploadNotFoundException(
                $uploadDirectory,
                $uploadId,
                previous: $e,
            );
        }
    }

    public function id(): string
    {
        return $this->upload->name();
    }

    /**
     * @throws UploadReadingException
     */
    public function originalName(): string
    {
        try {
            $content = json_decode(
                $this->info->content(),
                associative: true,
                depth: JSON_THROW_ON_ERROR,
            );

            Assert::isArray($content, 'Invalid JSON');
            Assert::keyExists($content, 'MetaData', 'Missing [MetaData]');
            Assert::isArray($content['MetaData'], 'Invalid [MetaData]');
            Assert::keyExists($content['MetaData'], 'filename', 'Missing [MetaData][filename]');
            Assert::string($content['MetaData']['filename'], 'Invalid [MetaData][filename]');

            return $content['MetaData']['filename'];
        } catch (Throwable $e) {
            throw new UploadReadingException(
                path: $this->info->path(),
                message: $e->getMessage(),
                previous: $e,
            );
        }
    }

    /**
     * @throws UploadReadingException
     */
    public function extension(): string
    {
        try {
            return $this->upload->extension();
        } catch (LogicException $e) {
            throw new UploadReadingException(
                path: $this->upload->path(),
                message: $e->getMessage(),
                previous: $e,
            );
        }
    }

    /**
     * @throws UploadReadingException
     */
    public function mimeType(): string
    {
        try {
            return $this->upload->mimeType();
        } catch (LogicException $e) {
            throw new UploadReadingException(
                path: $this->upload->path(),
                message: $e->getMessage(),
                previous: $e,
            );
        }
    }

    /**
     * @throws UploadReadingException
     */
    public function bytes(): int
    {
        try {
            return $this->upload->size();
        } catch (RuntimeException $e) {
            throw new UploadReadingException(
                path: $this->upload->path(),
                message: $e->getMessage(),
                previous: $e,
            );
        }
    }

    /**
     * It tries to move the uploaded file to the content directory with the correct name and extension
     * and removes the .info file afterwards.
     *
     * If the uploaded file moving fails, it tries to remove both files.
     * If the .info removal fails, it tries to remove the moved file.
     *
     * @throws UploadConsumptionException
     */
    public function move(string $destinationDirectory, File $file): void
    {
        /**
         * Move the uploaded file to the content directory with the correct name and extension
         */
        try {
            $moved = $this->upload->move($destinationDirectory, $file);
        } catch (FileException $uploadMovingException) {
            /**
             * Move of the uploaded file failed, try to remove both files
             */
            $exception = new UploadConsumptionException(
                originPath: $this->upload->path(),
                destinationPath: $file->filePath($destinationDirectory),
                previous: $uploadMovingException,
            );

            try {
                $this->upload->remove();
            } catch (IOException $uploadRemovalException) {
                $exception->aggregateException(
                    new UploadRemovalException(
                        path: $this->upload->path(),
                        previous: $uploadRemovalException,
                    ),
                );
            }

            try {
                $this->info->remove();
            } catch (IOException $infoRemovalException) {
                $exception->aggregateException(
                    new UploadRemovalException(
                        path: $this->info->path(),
                        previous: $infoRemovalException,
                    ),
                );
            }

            throw $exception;
        }

        /**
         * Remove the .info tusd file
         */
        try {
            $this->info->remove();
        } catch (IOException $infoRemovalException) {
            /**
             * Removal of the .info file failed, remove the moved uploaded file
             */
            $exception = new UploadConsumptionException(
                originPath: $this->upload->path(),
                destinationPath: $file->filePath($destinationDirectory),
                previous: $infoRemovalException,
            );

            try {
                $moved->remove();
            } catch (IOException $uploadRemovalException) {
                $exception->aggregateException(
                    new UploadRemovalException(
                        path: $moved->path(),
                        previous: $uploadRemovalException,
                    ),
                );
            }

            throw $exception;
        }
    }

    /**
     * @throws UploadRemovalException
     */
    public function remove(): void
    {
        try {
            $this->upload->remove();
        } catch (IOException $e) {
            throw new UploadRemovalException(
                path: $this->upload->path(),
                previous: $e,
            );
        }

        try {
            $this->info->remove();
        } catch (IOException $e) {
            throw new UploadRemovalException(
                path: $this->info->path(),
                previous: $e,
            );
        }
    }
}
