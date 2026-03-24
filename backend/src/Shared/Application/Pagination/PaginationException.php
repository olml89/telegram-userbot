<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Application\Pagination;

use RuntimeException;

final class PaginationException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
