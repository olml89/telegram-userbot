<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\PdfComplexityCheckers;

use JsonException;
use olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker\PdfPasswordProtectedException;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFile;
use olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Process\ItRunsExternalProcess;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final readonly class QpdfStructureChecker
{
    use ItRunsExternalProcess;

    /**
     * @throws PdfPasswordProtectedException
     * @throws ProcessFailedException
     * @throws JsonException
     */
    public function hasComplexStructure(StorageFile $storageFile): bool
    {
        $qpdf = new Process([
            'qpdf',
            '--json',
            $storageFile->getPathname(),
        ]);

        try {
            $output = $this->run($qpdf);
        } catch (ProcessFailedException $e) {
            /**
             * qpdf exit codes:
             * 0: success
             * 1: fatal error
             * 2: minor error (partial parsing), usually due to malformed PDF
             * 3: operation completed but with warnings (non-standard structures, atypical objects, etc.)
             */
            if ($e->getProcess()->getExitCode() === 1) {
                throw $e;
            }

            /**
             * Failed while trying to process an encrypted PDF
             */
            if (str_contains($e->getProcess()->getErrorOutput(), 'invalid password')) {
                throw new PdfPasswordProtectedException($e);
            }

            /**
             * Extract the content from the exception message:
             * Output
             * ================
             * {OUTPUT}
             * Error Output:
             * ================
             */
            $start = strpos($e->getMessage(), $startMarker = "Output:\n================");
            $end = strpos($e->getMessage(), "Error Output:\n================");

            if ($start === false || $end === false || $end <= $start) {
                throw $e;
            }

            $start += strlen($startMarker);
            $output = trim(substr($e->getMessage(), $start, length: $end - $start));
        }

        /**
         * @var array{
         *     acroform?: array{
         *         hasacroform?: bool
         *     },
         *     attachments?: array<string, mixed>,
         *     qpdf: array{
         *         0?: array<string, mixed>,
         *         1?: array<string, array<string, array<string, mixed>>>
         *     }
         * } $json
         */
        $json = $this->decodeJsonOutput($output);
        $hasAcroform = ($json['acroform']['hasacroform'] ?? false) === true;
        $hasAttachments = is_array($json['attachments'] ?? null) && count($json['attachments']) > 0;
        $hasJavascript = $this->hasJavascript($json['qpdf'][1] ?? []);

        return $hasAcroform || $hasAttachments || $hasJavascript;
    }

    /**
     * @param array<int|string, mixed> $data
     */
    private function hasJavascript(array $data): bool
    {
        foreach ($data as $key => $value) {
            if ($this->isJavaScriptDict($value)) {
                return true;
            }

            if (is_array($value) && $this->hasJavascript($value)) {
                return true;
            }

            if (is_string($key) && strcasecmp($key, '/OpenAction') === 0) {
                return true;
            }
        }

        return false;
    }

    private function isJavaScriptDict(mixed $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        if (($value['/S'] ?? null) === '/JavaScript') {
            return true;
        }

        return array_key_exists('/JS', $value);
    }
}
