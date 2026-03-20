<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\FileSpecializers;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Media\Video as FFMpegVideo;
use InvalidArgumentException;
use LogicException;
use olml89\TelegramUserbot\Backend\File\Domain\Duration\Duration;
use olml89\TelegramUserbot\Backend\File\Domain\Duration\DurationException;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileName;
use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileNameLengthException;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\FileSpecializationException;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\VideoSpecializer;
use olml89\TelegramUserbot\Backend\File\Domain\Resolution\Resolution;
use olml89\TelegramUserbot\Backend\File\Domain\Resolution\ResolutionException;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFile;
use olml89\TelegramUserbot\Backend\File\Domain\Video;
use RuntimeException;
use Throwable;

final readonly class FfmpegVideoSpecializer implements VideoSpecializer
{
    public function __construct(
        private FileManager $fileManager,
        private FFMpeg $ffmpeg,
    ) {}

    /**
     * @throws FileSpecializationException
     */
    public function specialize(File $file): Video
    {
        try {
            $storageFile = $this->fileManager->storageFile($file);
            $ffmpegVideoFile = $this->ffmpeg->open($storageFile->getPathname());

            if (!$ffmpegVideoFile instanceof FFMpegVideo) {
                throw new RuntimeException('File is not a video');
            }

            return new Video(
                file: $file,
                thumbnail: $this->getThumbnail($file, $ffmpegVideoFile),
                duration: $this->getDuration($storageFile),
                resolution: $this->getResolution($ffmpegVideoFile),
            );
        } catch (Throwable $e) {
            throw new FileSpecializationException($e);
        }
    }

    /**
     * @throws RuntimeException
     * @throws FileNameLengthException
     */
    private function getThumbnail(File $file, FFMpegVideo $ffmpegVideoFile): FileName
    {
        $thumbnail = FileName::from(
            name: $file->publicId(),
            extension: 'jpg',
        );

        $timeCode = TimeCode::fromSeconds(0);
        $ffmpegVideoFile->frame($timeCode)->save($this->fileManager->path($thumbnail));

        return $thumbnail;
    }

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws DurationException
     */
    private function getDuration(StorageFile $storageFile): Duration
    {
        $format = $this->ffmpeg->getFFProbe()->format($storageFile->getPathname());
        $duration = $format->get('duration');

        if (!is_numeric($duration)) {
            throw new RuntimeException('Duration is not numeric');
        }

        return new Duration((float) $duration);
    }

    /**
     * @throws LogicException
     * @throws RuntimeException
     * @throws ResolutionException
     */
    private function getResolution(FFMpegVideo $ffmpegVideoFile): Resolution
    {
        $videoStream = $ffmpegVideoFile->getStreams()->videos()->first();

        if (is_null($videoStream)) {
            throw new RuntimeException('Video stream not found');
        }

        $dimensions = $videoStream->getDimensions();
        $width = $dimensions->getWidth();
        $height = $dimensions->getHeight();

        return new Resolution($width, $height);
    }
}
