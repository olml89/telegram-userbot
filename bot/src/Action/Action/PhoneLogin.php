<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action\Action;

use olml89\TelegramUserbot\Bot\Action\Action;
use olml89\TelegramUserbot\Bot\Bot\BotConfig;
use olml89\TelegramUserbot\Bot\MadelineProto\ApiWrapper;
use olml89\TelegramUserbot\Bot\MadelineProto\IpcWorkerOutputCatcherFactory;

final readonly class PhoneLogin implements Action
{
    public function __construct(
        private BotConfig $botConfig,
        private IpcWorkerOutputCatcherFactory $ipcWorkerOutputCatcherFactory,
    ) {}

    public function run(ApiWrapper $apiWrapper): void
    {
        $this->ipcWorkerOutputCatcherFactory
            ->create($apiWrapper, $apiWrapper->phoneLogin(...))
            ->run($this->botConfig->phoneNumber);
    }
}
