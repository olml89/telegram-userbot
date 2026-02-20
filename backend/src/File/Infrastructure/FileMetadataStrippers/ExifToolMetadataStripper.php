<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\FileMetadataStrippers;

use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\FileMetadataStrippingException;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\PdfMetadataStripper;
use olml89\TelegramUserbot\Backend\File\Domain\Pdf;
use olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Process\ItRunsExternalProcess;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final readonly class ExifToolMetadataStripper implements PdfMetadataStripper
{
    use ItRunsExternalProcess;

    public function __construct(
        private FileManager $fileManager,
    ) {}

    /**
     * @throws FileMetadataStrippingException
     */
    public function strip(Pdf $pdf): bool
    {
        try {
            $storageFile = $this->fileManager->storageFile($pdf);

            $exif = new Process([
                'exiftool',
                '-all=',
                '-overwrite_original',
                $storageFile->getPathname(),
            ]);

            $this->run($exif);

            return true;
        } catch (ProcessFailedException $e) {
            throw new FileMetadataStrippingException($e);
        }
    }
}
