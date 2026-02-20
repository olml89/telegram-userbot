<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileMetadataStripper;

use olml89\TelegramUserbot\Backend\File\Domain\Pdf;

interface PdfMetadataStripper
{
    /** @throws FileMetadataStrippingException */
    public function strip(Pdf $pdf): bool;
}
