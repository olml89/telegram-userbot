<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\App\Environment;

use Exception;

final class MissingEnvironmentVariableException extends Exception
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('%s not found in $_ENV', $name));
    }
}
