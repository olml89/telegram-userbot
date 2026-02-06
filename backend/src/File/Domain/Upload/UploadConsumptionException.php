<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Upload;

use Exception;
use Throwable;

final class UploadConsumptionException extends Exception
{
    public function __construct(string $originPath, string $destinationPath, ?Throwable $e)
    {
        parent::__construct(
            message: sprintf(
                'Error saving upload %s to %s',
                $originPath,
                $destinationPath,
            ),
            previous: $e,
        );
    }
}
