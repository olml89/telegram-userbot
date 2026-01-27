<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\UploadedFile;

use olml89\TelegramUserbot\Backend\Content\Domain\File;
use olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile\UploadedFile;
use olml89\TelegramUserbot\Backend\Content\Domain\UploadedFile\UploadedFileException;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyFoundationUploadedFile;
use Symfony\Component\Uid\Uuid;
use Throwable;

final class SymfonyUploadedFile implements UploadedFile
{
    private const string DEFAULT_EXTENSION = 'bin';
    private const string DEFAULT_MIME_TYPE = 'application/octet-stream';

    private readonly SymfonyFoundationUploadedFile $file;
    private readonly string $name;
    private readonly string $originalName;
    private readonly string $mimeType;
    private readonly int $size;
    private bool $saved = false;

    public function __construct(SymfonyFoundationUploadedFile $file)
    {
        $this->file = $file;

        $this->name = sprintf(
            '%s.%s',
            Uuid::v4()->toRfc4122(),
            $this->file->guessClientExtension() ?? self::DEFAULT_EXTENSION,
        );

        $this->originalName = $this->file->getClientOriginalName();
        $this->mimeType = $this->file->getMimeType() ?? self::DEFAULT_MIME_TYPE;
        $this->size = $this->file->getSize();
    }

    /**
     * @throws UploadedFileException
     */
    public function save(string $contentDirectory): File
    {
        $file = new File(
            name: $this->name,
            originalName: $this->originalName,
            mimeType: $this->mimeType,
            size: $this->size,
        );

        if ($this->saved) {
            throw UploadedFileException::alreadySaved($file);
        }

        try {
            $this->file->move($file->path($contentDirectory), $this->name);
            $this->saved = true;

            return $file;
        } catch (Throwable $e) {
            throw UploadedFileException::errorSaving($file, $contentDirectory, $e);
        }
    }
}
