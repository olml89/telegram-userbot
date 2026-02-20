<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\PdfComplexityCheckers;

use olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker\PdfPasswordProtectedException;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFile;
use olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Process\ItRunsExternalProcess;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final readonly class PdfsigSignatureChecker
{
    use ItRunsExternalProcess;

    /**
     * @throws PdfPasswordProtectedException
     * @throws ProcessFailedException
     */
    public function hasSignature(StorageFile $storageFile): bool
    {
        $pdfSig = new Process([
            'pdfsig',
            $storageFile->getPathname(),
        ]);

        try {
            $output = $this->run($pdfSig);

            /**
             * Double-check output
             */
            if (str_contains($output, 'Signature #')) {
                return true;
            }
        } catch (ProcessFailedException $e) {
            /**
             * Failed while trying to process an encrypted PDF
             */
            if (str_contains($e->getMessage(), 'Command Line Error: Incorrect password')) {
                throw new PdfPasswordProtectedException($e);
            }

            /**
             * pdfsig throws an exception if no signature is found: ignore the exception in this case, throw it otherwise
             */
            if (!str_contains($e->getMessage(), 'does not contain any signatures')) {
                throw $e;
            }
        }

        return false;
    }
}
