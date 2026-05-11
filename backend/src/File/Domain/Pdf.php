<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker\PdfComplexityChecker;
use olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker\PdfPasswordProtectedException;
use olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker\PdfReadingException;

final class Pdf extends File
{
    public function __construct(UnattachedFile $unattachedFile)
    {
        parent::__construct(
            $unattachedFile->file()->publicId(),
            $unattachedFile->file()->fileName(),
            $unattachedFile->file()->originalName(),
            $unattachedFile->file()->mimeType(),
            $unattachedFile->file()->bytes(),
        );
    }

    /**
     * @throws PdfPasswordProtectedException
     * @throws PdfReadingException
     */
    public function isComplex(PdfComplexityChecker $pdfComplexityChecker): bool
    {
        return $pdfComplexityChecker->isComplex($this);
    }
}
