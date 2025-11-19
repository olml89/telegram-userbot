<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use olml89\TelegramUserbot\Bot\Action\LogRecord\ActionFinished;
use olml89\TelegramUserbot\Bot\Action\LogRecord\ActionStarted;
use olml89\TelegramUserbot\Bot\Bot\Status\StatusBroadcaster;
use olml89\TelegramUserbot\Bot\Bot\Status\ApiStatusCalculator;
use olml89\TelegramUserbot\Bot\MadelineProto\ApiBuilder;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessManager;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStartedException;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessType;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\ErrorLogRecord;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use Throwable;

/**
 * Wrapper to run Actions in a safe context. Log exceptions to avoid breaking down the bot process.
 */
final readonly class ActionRunner
{
    public function __construct(
        private ApiBuilder $apiBuilder,
        private ApiStatusCalculator $apiStatusCalculator,
        private StatusBroadcaster $statusBroadcaster,
        private LoggableLogger $loggableLogger,
        private ProcessManager $processManager,
    ) {
    }

    public function run(Action $action): void
    {
        $api = $this->apiBuilder->build();

        if (is_null($api)) {
            return;
        }

        $currentStatus = $this->apiStatusCalculator->calculate($api);

        try {
            $this->loggableLogger->log(new ActionStarted($action));
            $action->run($api);
            $this->loggableLogger->log(new ActionFinished($action));
        } catch (Throwable $e) {
            $this->statusBroadcaster->emit($currentStatus->withMessage($e->getMessage()));
            $this->loggableLogger->log(new ErrorLogRecord('Error running action', $e));

            /**
             * Clean-up
             *
             * At this point, the API object is broken.
             * Launch the request-status script in a separate non-blocking process.
             */
            try {
                $this->processManager->start(ProcessType::RequestStatus);
            } catch (ProcessNotStartedException $e) {
                $this->loggableLogger->log(new ErrorLogRecord('Error running cleanup', $e));
            }
        }
    }
}
