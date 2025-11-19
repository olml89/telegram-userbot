<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action\Action;

use danog\MadelineProto\API;
use olml89\TelegramUserbot\Bot\Action\Action;
use olml89\TelegramUserbot\Bot\MadelineProto\IpcWorkerOutputCatcherFactory;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\InvalidPhoneCodeException;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCodeStorage;
use olml89\TelegramUserbot\Shared\Redis\RedisStorageException;

final readonly class CompletePhoneLogin implements Action
{
    public function __construct(
        private PhoneCodeStorage $phoneCodeStorage,
        private IpcWorkerOutputCatcherFactory $ipcWorkerOutputCatcherFactory,
    ) {
    }

    /**
     * @throws RedisStorageException
     * @throws InvalidPhoneCodeException
     */
    public function run(API $api): void
    {
        $phoneCode = $this->phoneCodeStorage->retrieve();

        $this
            ->ipcWorkerOutputCatcherFactory
            ->create($api, $api->completePhoneLogin(...))
            ->run((string)$phoneCode);
    }
}
