<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action\Action;

use olml89\TelegramUserbot\Bot\Action\Action;
use olml89\TelegramUserbot\Bot\Action\IsAction;
use olml89\TelegramUserbot\Bot\Bot\BotSession;
use olml89\TelegramUserbot\Bot\MadelineProto\ApiWrapper;
use olml89\TelegramUserbot\Bot\MadelineProto\IpcWorkerOutputCatcherFactory;

final readonly class Logout implements Action
{
    use IsAction;

    public function __construct(
        private BotSession $botSession,
        private IpcWorkerOutputCatcherFactory $ipcWorkerOutputCatcherFactory,
    ) {}

    public function run(ApiWrapper $apiWrapper): void
    {
        // Reset the session so the MadelineProto IPC doesn't hang
        $this->botSession->reset();

        $this
            ->ipcWorkerOutputCatcherFactory
            ->create($apiWrapper, $apiWrapper->logout(...))
            ->run();
    }
}
