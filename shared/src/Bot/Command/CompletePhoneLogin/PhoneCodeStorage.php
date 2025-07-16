<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin;

use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\LogRecord\DeletedPhoneCode;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\LogRecord\RetrievedPhoneCode;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\LogRecord\StoredPhoneCode;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use olml89\TelegramUserbot\Shared\Redis\RedisConfig;
use olml89\TelegramUserbot\Shared\Redis\RedisStorage;
use olml89\TelegramUserbot\Shared\Redis\RedisStorageException;

/**
 * It stores or retrieves a PhoneCode from Redis.
 */
final readonly class PhoneCodeStorage
{
    public function __construct(
        private RedisConfig $config,
        private RedisStorage $storage,
        private LoggableLogger $loggableLogger,
    ) {
    }

    /**
     * @throws RedisStorageException
     * @throws InvalidPhoneCodeException
     */
    public function retrieve(): PhoneCode
    {
        if (is_null($phoneCodeValue = $this->storage->get($this->config->phoneCodeStorageKey))) {
            throw RedisStorageException::get($this->config->phoneCodeStorageKey);
        }

        $phoneCode = new PhoneCode($phoneCodeValue);
        $this->loggableLogger->log(new RetrievedPhoneCode($phoneCode, $this->config->phoneCodeStorageKey));

        $this->storage->del($this->config->phoneCodeStorageKey);
        $this->loggableLogger->log(new DeletedPhoneCode($phoneCode, $this->config->phoneCodeStorageKey));

        return $phoneCode;
    }

    /**
     * @throws RedisStorageException
     */
    public function store(PhoneCode $phoneCode): void
    {
        $this->storage->set($this->config->phoneCodeStorageKey, $phoneCode);
        $this->loggableLogger->log(new StoredPhoneCode($phoneCode, $this->config->phoneCodeStorageKey));
    }
}
