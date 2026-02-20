<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\PdfComplexityChecker;

use Exception;
use Throwable;

final class PdfPasswordProtectedException extends Exception
{
    public function __construct(Throwable $previous)
    {
        parent::__construct(
            message: 'Error reading pdf file: is password protected',
            previous: $previous,
        );
    }
}
