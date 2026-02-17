<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\GetId3;

use getID3;
use olml89\TelegramUserbot\Backend\File\Domain\Audio;
use olml89\TelegramUserbot\Backend\File\Domain\Duration\Duration;
use olml89\TelegramUserbot\Backend\File\Domain\Duration\DurationException;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\AudioSpecializer;
use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\FileSpecializationException;

final readonly class GetId3AudioSpecializer implements AudioSpecializer
{
    public function __construct(
        private FileManager $fileManager,
        private getID3 $getID3,
    ) {}

    /**
     * @throws FileSpecializationException
     */
    public function create(File $file): Audio
    {
        try {
            return $this->createAudio($file);
        } catch (DurationException $e) {
            throw new FileSpecializationException($e);
        }
    }

    /**
     * @throws DurationException
     */
    private function createAudio(File $file): Audio
    {
        $audioFile = $this->fileManager->mediaFile($file);
        $fileInfo = $this->getID3->analyze($audioFile->getPathname());
        $playtimeSeconds = $fileInfo['playtime_seconds'] ?? null;

        if (is_null($playtimeSeconds)) {
            throw DurationException::missing();
        }

        if (!is_float($playtimeSeconds)) {
            throw DurationException::invalid();
        }

        $duration = new Duration($playtimeSeconds);

        $audio = new Audio(
            publicId: $file->publicId(),
            name: $file->name(),
            originalName: $file->originalName(),
            mimeType: $file->mimeType(),
            bytes: $file->bytes(),
            duration: $duration,
        );

        return $audio->copyEvents($file);
    }
}
