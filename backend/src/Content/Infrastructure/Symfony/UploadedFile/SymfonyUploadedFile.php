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

    public function name(): string
    {
        return $this->name;
    }

    public function originalName(): string
    {
        return $this->originalName;
    }

    public function mimeType(): string
    {
        return $this->mimeType;
    }

    public function size(): int
    {
        return $this->size;
    }

    /**
     * @throws UploadedFileException
     */
    public function save(string $contentDirectory): File
    {
        if ($this->saved) {
            throw UploadedFileException::alreadySaved($this);
        }

        try {
            $path = sprintf(
                '%s/%s/%s',
                $contentDirectory,
                substr($this->name, 0, 2),
                substr($this->name, 2, 2),
            );

            $this->file->move($path, $this->name);
            $this->saved = true;

            return new File(
                name: $this->name(),
                originalName: $this->originalName(),
                mimeType: $this->mimeType(),
                size: $this->size(),
            );
        } catch (Throwable $e) {
            throw UploadedFileException::errorSaving($this, $path, $e);
        }
    }
}
