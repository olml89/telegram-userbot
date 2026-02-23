<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain;

use olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker\PdfComplexityChecker;
use olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker\PdfPasswordProtectedException;
use olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker\PdfReadingException;

final class Pdf extends File
{
    public function __construct(File $file)
    {
        parent::__construct(
            $file->publicId(),
            $file->fileName(),
            $file->originalName(),
            $file->mimeType(),
            $file->bytes(),
        );

        $this->copyEvents($file);
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
