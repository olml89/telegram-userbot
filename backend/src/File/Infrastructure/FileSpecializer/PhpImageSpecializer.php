<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\FileSpecializer;

use olml89\TelegramUserbot\Backend\File\Domain\File;
use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\FileSpecializationException;
use olml89\TelegramUserbot\Backend\File\Domain\FileSpecializer\ImageSpecializer;
use olml89\TelegramUserbot\Backend\File\Domain\Image;
use olml89\TelegramUserbot\Backend\File\Domain\Resolution\Resolution;
use olml89\TelegramUserbot\Backend\File\Domain\Resolution\ResolutionException;
use RuntimeException;
use SplFileObject;

final readonly class PhpImageSpecializer implements ImageSpecializer
{
    public function __construct(
        private FileManager $fileManager,
    ) {}

    /**
     * @throws FileSpecializationException
     */
    public function specialize(File $file): Image
    {
        try {
            $imageFile = $this->fileManager->mediaFile($file);
            [$width, $height] = $this->getImageSize($imageFile);
            $resolution = new Resolution($width, $height);

            return new Image($file, $resolution);
        } catch (RuntimeException|ResolutionException $e) {
            throw new FileSpecializationException($e);
        }
    }

    /**
     * @return array{
     *     0: int<0, max>,
     *     1: int<0, max>,
     *     2: int,
     *     3: string,
     *     bits?: int,
     *     channels?: int,
     *     mime: string,
     * }
     *
     * @throws RuntimeException
     * @throws ResolutionException
     */
    private function getImageSize(SplFileObject $imageFile): array
    {
        $imageSize = @getimagesize($imageFile->getPathname());

        if ($imageSize === false) {
            throw new RuntimeException('Unable to get image size');
        }

        return $imageSize;
    }
}
