<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\FileMetadataStrippers;

use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\FileMetadataStrippingException;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\VideoMetadataStripper;
use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileName;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFile;
use olml89\TelegramUserbot\Backend\File\Domain\Video;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Uid\Uuid;
use Throwable;

final readonly class FfmpegVideoMetadataStripper implements VideoMetadataStripper
{
    public function __construct(
        private FileManager $fileManager,
    ) {}

    /**
     * @throws FileMetadataStrippingException
     */
    public function strip(Video $video): true
    {
        try {
            $storageFile = $this->fileManager->storageFile($video);

            /**
             * tmp StorageFile name: random UUID, same extension as the original StorageFile
             * (ffmpeg deducts the format of the container from the extension of the output file)
             */
            $tmpFileName = FileName::from(
                name: Uuid::v4(),
                extension: $storageFile->getExtension(),
            );

            $tmpFile = new StorageFile(
                sprintf(
                    '%s/%s',
                    $storageFile->getPath(),
                    $tmpFileName->value,
                ),
            );

            $process = new Process([
                'ffmpeg',
                '-y',
                '-i', $storageFile->getPathname(),
                '-map_metadata', '-1',
                '-map_chapters', '-1',
                '-c', 'copy',
                $tmpFile->getPathname(),
            ]);

            $process->run();

            if (!$process->isSuccessful()) {
                $this->fileManager->remove($tmpFile);
                throw new ProcessFailedException($process);
            }

            $tmpFile->move($storageFile);

            return true;
        } catch (Throwable $e) {
            throw new FileMetadataStrippingException($e);
        }
    }
}
