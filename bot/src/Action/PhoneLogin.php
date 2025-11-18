<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use danog\MadelineProto\API;
use olml89\TelegramUserbot\Bot\Bot\BotConfig;
use olml89\TelegramUserbot\Bot\MadelineProto\IpcWorkerOutputCatcherFactory;

final readonly class PhoneLogin implements Action
{
    public function __construct(
        private BotConfig $botConfig,
        private IpcWorkerOutputCatcherFactory $ipcWorkerOutputCatcherFactory,
    ) {
    }

    public function run(API $api): void
    {
        $this->ipcWorkerOutputCatcherFactory
            ->create($api, $api->phoneLogin(...))
            ->run($this->botConfig->phoneNumber);
    }
}
