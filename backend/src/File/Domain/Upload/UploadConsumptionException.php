<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\Upload;

use Exception;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\ExceptionAggregator;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\IsExceptionAggregator;
use Throwable;

final class UploadConsumptionException extends Exception implements ExceptionAggregator
{
    use IsExceptionAggregator;

    public function __construct(string $originPath, string $destinationPath, Throwable $previous)
    {
        parent::__construct(
            message: sprintf(
                'Error saving upload %s to %s',
                $originPath,
                $destinationPath,
            ),
            previous: $previous,
        );
    }
}
