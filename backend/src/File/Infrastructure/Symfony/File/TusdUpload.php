<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\File;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\Upload;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadConsumptionException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadInfoException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadNotFoundException;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Throwable;

final readonly class TusdUpload implements Upload
{
    private const string DEFAULT_EXTENSION = 'bin';
    private const string DEFAULT_MIME_TYPE = 'application/octet-stream';

    private string $uploadId;
    private SymfonyFile $file;
    private TusdUploadInfo $info;

    /**
     * @throws UploadNotFoundException
     */
    public function __construct(string $uploadDirectory, string $uploadId)
    {
        $this->uploadId = $uploadId;

        try {
            $this->file = new SymfonyFile(
                sprintf(
                    '%s/%s',
                    $uploadDirectory,
                    $uploadId,
                ),
            );
            $this->info = new TusdUploadInfo($uploadDirectory, $uploadId);
        } catch (Throwable $e) {
            throw new UploadNotFoundException(
                $uploadDirectory,
                $uploadId,
                $e,
            );
        }
    }

    public function id(): string
    {
        return $this->uploadId;
    }

    /**
     * @throws UploadInfoException
     */
    public function originalName(): string
    {
        return $this->info->originalName();
    }

    public function extension(): string
    {
        return $this->file->guessExtension() ?? self::DEFAULT_EXTENSION;
    }

    public function mimeType(): string
    {
        return $this->file->getMimeType() ?? self::DEFAULT_MIME_TYPE;
    }

    public function bytes(): int
    {
        return $this->file->getSize();
    }

    /**
     * @throws UploadConsumptionException
     */
    public function move(string $destinationDirectory, File $file): void
    {
        try {
            /**
             * Move the uploaded file to the content directory with the correct name and extension
             */
            $this->file->move($destinationDirectory, $file->name()->value);

            /**
             * Delete the .info tusd file
             */
            $this->info->remove();
        } catch (Throwable $e) {
            throw new UploadConsumptionException(
                originPath: $this->file->getPathname(),
                destinationPath: sprintf(
                    '%s/%s',
                    $destinationDirectory,
                    $file->name()->value,
                ),
                e: $e,
            );
        }
    }
}
