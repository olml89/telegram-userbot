<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use olml89\TelegramUserbot\Bot\Action\LogRecord\ActionFinished;
use olml89\TelegramUserbot\Bot\Action\LogRecord\ActionStarted;
use olml89\TelegramUserbot\Bot\MadelineProto\ApiInitializer;
use olml89\TelegramUserbot\Bot\MadelineProto\ApiWrapper;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use Throwable;

/**
 * Wrapper to run Actions in a safe context. Log exceptions to avoid breaking down the bot process.
 */
final readonly class ActionRunner
{
    public function __construct(
        private ApiWrapper $apiWrapper,
        private ApiInitializer $apiInitializer,
        private LoggableLogger $loggableLogger,
        private ErrorHandler $errorHandler,
    ) {
    }

    public function run(Action $action): void
    {
        $this->loggableLogger->log(new ActionStarted($action));

        if (!$this->apiInitializer->initialize($this->apiWrapper)) {
            return;
        }

        $currentStatus = $this->apiWrapper->status();

        try {
            $action->run($this->apiWrapper);
            $this->loggableLogger->log(new ActionFinished($action));
        } catch (Throwable $e) {
            $this->errorHandler->handle($action, $currentStatus, $e);
        }
    }
}
