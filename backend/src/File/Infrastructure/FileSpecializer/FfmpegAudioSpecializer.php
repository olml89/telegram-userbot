<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\FileSpecializer;

use olml89\TelegramUserbot\Backend\File\Domain\Audio;
use olml89\TelegramUserbot\Backend\File\Domain\Duration\Duration;
use olml89\TelegramUserbot\Backend\File\Domain\Duration\DurationException;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\AudioSpecializer;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\FileSpecializationException;
use Symfony\Component\Process\Process;

final readonly class FfmpegAudioSpecializer implements AudioSpecializer
{
    public function __construct(
        private FileManager $fileManager,
    ) {}

    /**
     * @throws FileSpecializationException
     */
    public function specialize(File $file): Audio
    {
        try {
            $audioFile = $this->fileManager->mediaFile($file);

            $process = new Process([
                'ffprobe',
                '-v', 'error',
                '-show_entries', 'format=duration',
                '-of', 'default=noprint_wrappers=1:nokey=1',
                $audioFile->getPathname(),
            ]);

            $process->mustRun();
            $duration = new Duration((float) trim($process->getOutput()));

            return new Audio($file, $duration);
        } catch (DurationException $e) {
            throw new FileSpecializationException($e);
        }
    }
}
