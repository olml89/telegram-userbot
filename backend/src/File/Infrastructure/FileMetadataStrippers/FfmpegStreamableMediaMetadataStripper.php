<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\FileMetadataStrippers;

use olml89\TelegramUserbot\Backend\File\Domain\Audio;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\FileMetadataStrippingException;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\StreamableMediaMetadataStripper;
use olml89\TelegramUserbot\Backend\File\Domain\Video;
use olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Process\ItRunsExternalProcess;
use Symfony\Component\Process\Process;
use Throwable;

final readonly class FfmpegStreamableMediaMetadataStripper implements StreamableMediaMetadataStripper
{
    use ItRunsExternalProcess;

    public function __construct(
        private FileManager $fileManager,
    ) {}

    /**
     * @throws FileMetadataStrippingException
     */
    public function strip(Audio|Video $streamableMedia): true
    {
        try {
            $storageFile = $this->fileManager->storageFile($streamableMedia);
            $tmpFile = $this->createTemporaryFile($storageFile);

            $ffmpeg = new Process([
                'ffmpeg',
                '-y',
                '-i', $storageFile->getPathname(),
                '-map_metadata', '-1',
                '-map_chapters', '-1',
                '-c', 'copy',
                $tmpFile->getPathname(),
            ]);

            $this->run($ffmpeg, fn() => $this->fileManager->remove($storageFile));
            $tmpFile->move($storageFile);

            return true;
        } catch (Throwable $e) {
            throw new FileMetadataStrippingException($e);
        }
    }
}
