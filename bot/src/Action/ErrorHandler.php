<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use Amp\SignalException;
use olml89\TelegramUserbot\Bot\Action\LogRecord\ActionFinished;
use olml89\TelegramUserbot\Bot\Bot\Status\StatusBroadcaster;
use olml89\TelegramUserbot\Shared\Bot\Status\Status;
use olml89\TelegramUserbot\Shared\Error\SentryReporter;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\ErrorLogRecord;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use Throwable;

/**
 * Handle errors while handling actions.
 *
 * If the action process is stopped due to receiving a SIGNAL, it broadcasts the shutdown and escapes gracefully.
 *
 * If the action process is stopped unexpectedly, it registers the error and starts the clean-up process.
 */
final readonly class ErrorHandler
{
    public function __construct(
        private StatusBroadcaster $statusBroadcaster,
        private LoggableLogger $loggableLogger,
        private SentryReporter $sentryReporter,
        private CleanupRunner $cleanupRunner,
    ) {}

    public function handle(Action $action, Status $currentStatus, Throwable $e): void
    {
        if ($e->getPrevious() instanceof SignalException) {
            $this->statusBroadcaster->emit($currentStatus->withMessage($e->getPrevious()->getMessage()));
            $this->loggableLogger->log(new ActionFinished($action));

            return;
        }

        $this->statusBroadcaster->emit($currentStatus->withMessage($e->getMessage()));
        $this->loggableLogger->log(new ErrorLogRecord('Error running action', $e));
        $this->sentryReporter->report($e);

        /**
         * Clean-up: at this point, the API object is broken.
         * Launch the request-status script in a separate non-blocking process.
         */
        $this->cleanupRunner->run();
    }
}
