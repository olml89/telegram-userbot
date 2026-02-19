<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\File;

use LogicException;
use RuntimeException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

final readonly class File
{
    public function __construct(
        private SymfonyFile $file,
    ) {}

    /**
     * @throws FileNotFoundException
     */
    public static function from(string $directory, string $filename): self
    {
        return new self(new SymfonyFile(sprintf('%s/%s', $directory, $filename)));
    }

    /**
     * @throws FileException
     */
    public function content(): string
    {
        return $this->file->getContent();
    }

    /**
     * @throws LogicException
     */
    public function extension(): string
    {
        return $this->file->guessExtension() ?? throw new LogicException('File extension is not available');
    }

    /**
     * @throws LogicException
     */
    public function mimeType(): string
    {
        return $this->file->getMimeType() ?? throw new LogicException('File mime type is not available');
    }

    /**
     * @throws FileException
     */
    public function move(string $directory, string $name): self
    {
        return new self($this->file->move($directory, $name));
    }

    public function name(): string
    {
        return $this->file->getFilename();
    }

    public function path(): string
    {
        return $this->file->getPathname();
    }

    /**
     * @throws IOException
     */
    public function remove(): void
    {
        new Filesystem()->remove($this->file->getPathname());
    }

    /**
     * @throws RuntimeException
     */
    public function size(): int
    {
        if (($size = $this->file->getSize()) === false) {
            throw new RuntimeException('File size is not available');
        }

        return $size;
    }
}
