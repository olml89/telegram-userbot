<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use danog\MadelineProto\API;
use olml89\TelegramUserbot\Bot\Bot\BotSession;
use olml89\TelegramUserbot\Bot\MadelineProto\IpcWorkerOutputCatcherFactory;

final readonly class Logout implements Action
{
    public function __construct(
        private BotSession $botSession,
        private IpcWorkerOutputCatcherFactory $ipcWorkerOutputCatcherFactory,
    ) {
    }

    public function run(API $api): void
    {
        // Reset the session so the MadelineProto IPC doesn't hang
        $this->botSession->reset();

        $this
            ->ipcWorkerOutputCatcherFactory
            ->create($api, $api->logout(...))
            ->run();
    }
}
