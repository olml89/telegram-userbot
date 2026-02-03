<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\File;

use JsonException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadInfoException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadNotFoundException;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Throwable;

final readonly class TusdUploadInfo
{
    private SymfonyFile $info;

    /**
     * @throws UploadNotFoundException
     */
    public function __construct(string $uploadDirectory, string $uploadId)
    {
        try {
            $this->info = new SymfonyFile(
                sprintf(
                    '%s/%s.info',
                    $uploadDirectory,
                    $uploadId,
                ),
            );
        } catch (Throwable $e) {
            throw new UploadNotFoundException(
                $uploadDirectory,
                sprintf('%s.info', $uploadId),
                $e,
            );
        }
    }

    /**
     * @throws UploadInfoException
     */
    public function originalName(): string
    {
        try {
            $content = json_decode(
                $this->info->getContent(),
                associative: true,
                depth: JSON_THROW_ON_ERROR,
            );

            if (!is_array($content)) {
                throw UploadInfoException::fromMessage(
                    path: $this->info->getPathname(),
                    message: 'Decoded .info file is not an array',
                );
            }

            $filename = $content['MetaData']['filename'] ?? throw UploadInfoException::fromMessage(
                path: $this->info->getPathname(),
                message: 'Missing [MetaData][filename]',
            );

            if (!is_string($filename)) {
                throw UploadInfoException::fromMessage(
                    path: $this->info->getPathname(),
                    message: 'Invalid [MetaData][filename]',
                );
            }

            return $filename;
        } catch (JsonException $e) {
            throw UploadInfoException::fromException(
                path: $this->info->getPathname(),
                message: 'Invalid JSON',
                previous: $e,
            );
        }
    }

    public function remove(): void
    {
        unlink($this->info->getPathname());
    }
}
