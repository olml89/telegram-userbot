<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Status;

use Exception;

final class InvalidStatusException extends Exception
{
    public function __construct(StatusType $currentStatusType, StatusType ...$allowedStatusTypes)
    {
        parent::__construct(sprintf(
            'Current status: %s, expected status: [%s]',
            $currentStatusType->value,
            implode(
                ', ',
                array_map(
                    fn(StatusType $type): string => $type->value,
                    $allowedStatusTypes,
                ),
            ),
        ));
    }
}
