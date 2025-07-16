<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use danog\MadelineProto\Exception;
use olml89\TelegramUserbot\Bot\MadelineProto\ApiManager;
use olml89\TelegramUserbot\Bot\MadelineProto\IpcWorkerOutputCatcherFactory;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\InvalidPhoneCodeException;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCodeStorage;
use olml89\TelegramUserbot\Shared\Redis\RedisStorageException;

final readonly class CompletePhoneLogin implements Action
{
    public function __construct(
        private ApiManager $apiManager,
        private PhoneCodeStorage $phoneCodeStorage,
        private IpcWorkerOutputCatcherFactory $ipcWorkerOutputCatcherFactory,
    ) {
    }

    /**
     * @throws RedisStorageException
     * @throws InvalidPhoneCodeException
     * @throws Exception
     */
    public function run(): void
    {
        $api = $this->apiManager->build();
        $phoneCode = $this->phoneCodeStorage->retrieve();

        $this
            ->ipcWorkerOutputCatcherFactory
            ->create($api, $api->completePhoneLogin(...))
            ->run((string)$phoneCode);
    }
}
