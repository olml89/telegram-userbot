<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use danog\MadelineProto\API;
use danog\MadelineProto\Exception;
use olml89\TelegramUserbot\Bot\MadelineProto\ApiManager;
use olml89\TelegramUserbot\Bot\MadelineProto\BotEventHandler;

final readonly class Runner implements Action
{
    public function __construct(
        private ApiManager $apiManager,
    ) {
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $api = $this->apiManager->build();
        API::startAndLoopMulti([$api], BotEventHandler::class);
    }
}
