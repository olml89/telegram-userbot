<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use danog\MadelineProto\Exception;
use olml89\TelegramUserbot\Bot\MadelineProto\ApiManager;
use olml89\TelegramUserbot\Bot\MadelineProto\IpcWorkerOutputCatcherFactory;

final readonly class Logout implements Action
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

        // Reset the session so the MadelineProto IPC doesn't hang
        $this->apiManager->session->reset();

        $this
            ->ipcWorkerOutputCatcherFactory
            ->create($api, $api->logout(...))
            ->run();
    }
}
