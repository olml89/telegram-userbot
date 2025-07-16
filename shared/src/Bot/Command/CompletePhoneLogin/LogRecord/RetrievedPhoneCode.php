<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\LogRecord;

use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCode;

final readonly class RetrievedPhoneCode extends PhoneCodeLogRecord
{
    public function __construct(PhoneCode $phoneCode, string $storageKey)
    {
        parent::__construct($phoneCode, $storageKey, 'Retrieved phone code');
    }
}
