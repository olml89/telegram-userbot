<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\File;

use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadInfoException;
use olml89\TelegramUserbot\Backend\File\Domain\Upload\UploadNotFoundException;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Throwable;
use Webmozart\Assert\Assert;

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

            Assert::isArray($content, 'Invalid JSON');
            Assert::keyExists($content, 'MetaData', 'Missing [MetaData]');
            Assert::isArray($content['MetaData'], 'Invalid [MetaData]');
            Assert::keyExists($content['MetaData'], 'filename', 'Missing [MetaData][filename]');
            Assert::string($content['MetaData']['filename'], 'Invalid [MetaData][filename]');

            return $content['MetaData']['filename'];
        } catch (Throwable $e) {
            throw UploadInfoException::fromException(
                path: $this->info->getPathname(),
                message: $e->getMessage(),
                previous: $e,
            );
        }
    }

    public function remove(): void
    {
        unlink($this->info->getPathname());
    }
}
