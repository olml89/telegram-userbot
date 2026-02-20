<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\FileMetadataStrippers;

use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\FileMetadataStrippingException;
use olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper\PdfMetadataStripper;
use olml89\TelegramUserbot\Backend\File\Domain\Pdf;
use olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker\PdfComplexityChecker;
use olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker\PdfPasswordProtectedException;
use olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker\PdfReadingException;
use olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Process\ItRunsExternalProcess;

final readonly class CompoundPdfMetadataStripper implements PdfMetadataStripper
{
    use ItRunsExternalProcess;

    public function __construct(
        private PdfComplexityChecker $pdfReader,
        private GhostScriptMetadataStripper $ghostScriptMetadataStripper,
        private ExifToolMetadataStripper $exifToolMetadataStripper,
    ) {}

    /**
     * @throws FileMetadataStrippingException
     */
    public function strip(Pdf $pdf): bool
    {
        try {
            if (!$pdf->isComplex($this->pdfReader)) {
                $this->ghostScriptMetadataStripper->strip($pdf);
            }

            $this->exifToolMetadataStripper->strip($pdf);

            return true;
        } catch (PdfPasswordProtectedException) {
            return false;
        } catch (PdfReadingException $e) {
            throw new FileMetadataStrippingException($e);
        }
    }
}
