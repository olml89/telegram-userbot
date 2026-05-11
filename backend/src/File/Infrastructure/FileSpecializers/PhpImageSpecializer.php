<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\FileSpecializers;

use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\FileSpecializationException;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\ImageSpecializer;
use olml89\TelegramUserbot\Backend\File\Domain\Image;
use olml89\TelegramUserbot\Backend\File\Domain\Resolution\Resolution;
use olml89\TelegramUserbot\Backend\File\Domain\Resolution\ResolutionException;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFile;
use olml89\TelegramUserbot\Backend\File\Domain\UnattachedFile;
use RuntimeException;
use Throwable;

final readonly class PhpImageSpecializer implements ImageSpecializer
{
    public function __construct(
        private FileManager $fileManager,
    ) {}

    /**
     * @throws FileSpecializationException
     */
    public function specialize(UnattachedFile $unattachedFile): Image
    {
        try {
            $storageFile = $this->fileManager->storageFile($unattachedFile->file());

            return new Image(
                unattachedFile: $unattachedFile,
                resolution: $this->getResolution($storageFile),
            );
        } catch (Throwable $e) {
            throw new FileSpecializationException($e);
        }
    }

    /**
     * @throws RuntimeException
     * @throws ResolutionException
     */
    private function getResolution(StorageFile $storageFile): Resolution
    {
        $imageSize = @getimagesize($storageFile->getPathname());

        if ($imageSize === false) {
            throw new RuntimeException('Unable to get image size');
        }

        /**
         * @var array{
         *     0: int<0, max>,
         *     1: int<0, max>,
         *     2: int,
         *     3: string,
         *     bits?: int,
         *     channels?: int,
         *     mime: string,
         * } $imageSize
         */
        [$width, $height] = $imageSize;

        return new Resolution($width, $height);
    }
}
