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

final readonly class GhostScriptMetadataStripper implements PdfMetadataStripper
{
    use ItRunsExternalProcess;

    public function __construct(
        private FileManager $fileManager,
    ) {}

    /**
     * @throws FileMetadataStrippingException
     */
    public function strip(Pdf $file): bool
    {
        try {
            $storageFile = $this->fileManager->storageFile($file);
            $tmpFile = $this->createTemporaryFile($storageFile);

            $ghostscript = new Process([
                'gs',
                '-sDEVICE=pdfwrite',
                '-o', $tmpFile->getPathname(),

                // General behaviour
                '-dNOPAUSE',
                '-dBATCH',
                '-dQUIET',

                // Detection of duplicates
                '-dDetectDuplicateImages=true',

                // Fuentes: compression + subsetting
                '-dCompressFonts=true',
                '-dSubsetFonts=true',

                // Keep the original resolution (no downsampling)
                '-dDownsampleColorImages=false',
                '-dDownsampleGrayImages=false',
                '-dDownsampleMonoImages=false',
                '-dColorImageResolution=300',
                '-dGrayImageResolution=300',
                '-dMonoImageResolution=300',

                // Don't re-encode JPEG images
                '-dAutoFilterColorImages=true',
                '-dAutoFilterGrayImages=true',

                $storageFile->getPathname(),
            ]);

            $this->run($ghostscript, fn() => $this->fileManager->remove($tmpFile));
            $tmpFile->move($storageFile);

            return true;
        } catch (ProcessFailedException $e) {
            throw new FileMetadataStrippingException($e);
        }
    }
}
