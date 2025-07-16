<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use danog\MadelineProto\Exception;
use olml89\TelegramUserbot\Bot\MadelineProto\ApiManager;
use olml89\TelegramUserbot\Bot\MadelineProto\IpcWorkerOutputCatcherFactory;

final readonly class PhoneLogin implements Action
{
    public function __construct(
        private ApiManager $apiManager,
        private IpcWorkerOutputCatcherFactory $ipcWorkerOutputCatcherFactory,
    ) {
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $api = $this->apiManager->build();

        $this->ipcWorkerOutputCatcherFactory
            ->create($api, $api->phoneLogin(...))
            ->run($this->apiManager->config->phoneNumber);
    }
}
