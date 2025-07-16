<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\LogRecord;

use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCode;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\InfoLogRecord;

abstract readonly class PhoneCodeLogRecord extends InfoLogRecord
{
    public PhoneCode $phoneCode;
    public string $storageKey;

    public function __construct(PhoneCode $phoneCode, string $storageKey, string $message)
    {
        parent::__construct(message: $message);

        $this->phoneCode = $phoneCode;
        $this->storageKey = $storageKey;
    }

    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return [
            'storage-key' => $this->storageKey,
            'phone-code' => (string)$this->phoneCode,
        ];
    }
}
