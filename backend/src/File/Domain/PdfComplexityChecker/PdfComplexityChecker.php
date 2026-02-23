<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker;

use olml89\TelegramUserbot\Backend\File\Domain\Pdf;

interface PdfComplexityChecker
{
    /**
     * @throws PdfPasswordProtectedException
     * @throws PdfReadingException
     */
    public function isComplex(Pdf $pdf): bool;
}
