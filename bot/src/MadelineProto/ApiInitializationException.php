<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\MadelineProto;

use Exception;

final class ApiInitializationException extends Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function notInitialized(): self
    {
        return new self('The MadelineProto API is not initialized.');
    }

    public static function alreadyInitialized(): self
    {
        return new self('The MadelineProto API is already initialized.');
    }
}
