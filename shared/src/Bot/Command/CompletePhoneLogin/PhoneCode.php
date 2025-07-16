<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin;

use Stringable;

final readonly class PhoneCode implements Stringable
{
    private string $code;

    /**
     * @throws InvalidPhoneCodeException
     */
    public function __construct(mixed $code)
    {
        if (is_null($code)) {
            throw InvalidPhoneCodeException::missingCode();
        }

        if (!is_string($code)) {
            throw InvalidPhoneCodeException::notString();
        }

        if (mb_strlen($code) !== 5 || !is_numeric($code)) {
            throw InvalidPhoneCodeException::invalidCode($code);
        }

        $this->code = $code;
    }

    public function __toString(): string
    {
        return $this->code;
    }
}
