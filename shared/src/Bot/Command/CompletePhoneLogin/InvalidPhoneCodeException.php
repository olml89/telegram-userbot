<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin;

use Exception;

final class InvalidPhoneCodeException extends Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function missingCode(): self
    {
        return new self('OTP code is missing');
    }

    public static function notString(): self
    {
        return new self('Invalid OTP code: not a string');
    }

    /**
     * Telegram OTP codes are 5-digit numeric strings.
     */
    public static function invalidCode(string $invalidCode): self
    {
        return new self(sprintf(
            'Invalid OTP code: %s, OTP code must be a 5-digit numeric string',
            $invalidCode,
        ));
    }
}
