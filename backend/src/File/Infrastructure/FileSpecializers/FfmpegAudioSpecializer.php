<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\FileSpecializers;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe\DataMapping\Stream;
use olml89\TelegramUserbot\Backend\File\Domain\Audio;
use olml89\TelegramUserbot\Backend\File\Domain\Duration\Duration;
use olml89\TelegramUserbot\Backend\File\Domain\Duration\DurationException;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\AudioSpecializer;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\FileSpecializationException;
use RuntimeException;
use Throwable;

final readonly class FfmpegAudioSpecializer implements AudioSpecializer
{
    public function __construct(
        private FileManager $fileManager,
        private FFMpeg $ffmpeg,
    ) {}

    /**
     * @throws FileSpecializationException
     */
    public function specialize(File $file): Audio
    {
        try {
            $audioFile = $this->fileManager->mediaFile($file);
            $audioStream = $this->ffmpeg->open($audioFile->getPathname())->getStreams()->audios()->first();

            if (is_null($audioStream)) {
                throw new RuntimeException('Audio stream not found');
            }

            return new Audio(
                file: $file,
                duration: $this->getDuration($audioStream),
            );
        } catch (Throwable $e) {
            throw new FileSpecializationException($e);
        }
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
}
