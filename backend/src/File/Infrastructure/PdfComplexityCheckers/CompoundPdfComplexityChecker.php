<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\PdfComplexityCheckers;

use olml89\TelegramUserbot\Backend\File\Domain\FileManager;
use olml89\TelegramUserbot\Backend\File\Domain\Pdf;
use olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker\PdfComplexityChecker;
use olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker\PdfPasswordProtectedException;
use olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker\PdfReadingException;
use Throwable;

final readonly class CompoundPdfComplexityChecker implements PdfComplexityChecker
{
    public function __construct(
        private FileManager $fileManager,
        private PdfsigSignatureChecker $pdfsigSignatureChecker,
        private QpdfStructureChecker $qpdfComplexKeysChecker,
    ) {}

    /**
     * @throws PdfPasswordProtectedException
     * @throws PdfReadingException
     */
    public function isComplex(Pdf $pdf): bool
    {
        try {
            $storageFile = $this->fileManager->storageFile($pdf);

            return $this->pdfsigSignatureChecker->hasSignature($storageFile)
                || $this->qpdfComplexKeysChecker->hasComplexStructure($storageFile);
        } catch (PdfPasswordProtectedException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new PdfReadingException($e);
        }
    }
}
