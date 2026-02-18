<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\FileSpecializer;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe\DataMapping\Stream;
use FFMpeg\Media\Video as FFMpegVideo;
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
use olml89\TelegramUserbot\Backend\File\Domain\Video;
use RuntimeException;

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
            $videoFile = $this->fileManager->mediaFile($file);
            $ffmpegVideoFile = $this->ffmpeg->open($videoFile->getPathname());

            if (!$ffmpegVideoFile instanceof FFMpegVideo) {
                throw new RuntimeException('File is not a video');
            }

            $videoStream = $ffmpegVideoFile->getStreams()->videos()->first();

            if (is_null($videoStream)) {
                throw new RuntimeException('Video stream not found');
            }

            return new Video(
                file: $file,
                thumbnail: $this->getThumbnail($file, $ffmpegVideoFile),
                duration: $this->getDuration($videoStream),
                resolution: $this->getResolution($videoStream),
            );
        } catch (LogicException|RuntimeException|ResolutionException|DurationException $e) {
            throw new FileSpecializationException($e);
        }
    }

    /**
     * @throws RuntimeException
     * @throws FileNameLengthException
     */
    private function getThumbnail(File $file, FFMpegVideo $ffmpegVideo): FileName
    {
        $thumbnail = FileName::from(
            name: $file->publicId(),
            extension: 'jpg',
        );

        $timeCode = TimeCode::fromSeconds(0);
        $ffmpegVideo->frame($timeCode)->save($this->fileManager->path($thumbnail));

        return $thumbnail;
    }

    /**
     * @throws RuntimeException
     * @throws DurationException
     */
    private function getDuration(Stream $videoStream): Duration
    {
        $duration = $videoStream->get('duration');

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
    private function getResolution(Stream $videoStream): Resolution
    {
        $dimensions = $videoStream->getDimensions();
        $width = $dimensions->getWidth();
        $height = $dimensions->getHeight();

        return new Resolution($width, $height);
    }
}
