<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use danog\MadelineProto\Exception;
use olml89\TelegramUserbot\Bot\MadelineProto\ApiManager;

final readonly class RequestStatus implements Action
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
        $this->apiManager->build();
    }
}
