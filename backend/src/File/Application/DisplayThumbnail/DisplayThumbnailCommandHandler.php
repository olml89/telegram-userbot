<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Application\DisplayThumbnail;

use olml89\TelegramUserbot\Backend\File\Domain\FileFinder;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotFoundException;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotReadableException;
use olml89\TelegramUserbot\Backend\File\Domain\Thumbnail\ThumbnailDisplayer;
use olml89\TelegramUserbot\Backend\File\Domain\Thumbnail\ThumbnailNotFoundException;
use SplFileObject;

final readonly class DisplayThumbnailCommandHandler
{
    public function __construct(
        private FileFinder $fileFinder,
        private FileManager $fileManager,
    ) {}

    /**
     * @throws FileNotFoundException
     * @throws ThumbnailNotFoundException
     * @throws FileNotReadableException
     */
    public function handle(DisplayThumbnailCommand $command): SplFileObject
    {
        $file = $this->fileFinder->find($command->publicId);

        if (!$file instanceof ThumbnailDisplayer) {
            throw new ThumbnailNotFoundException($file);
        }

        return $this->fileManager->mediaFile($file->thumbnail());
    }
}
