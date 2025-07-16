<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\MadelineProto;

use Closure;
use danog\MadelineProto\API;
use olml89\TelegramUserbot\Bot\Bot\BotLogFile;
use olml89\TelegramUserbot\Bot\Bot\Status\StatusBroadcaster;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;

final readonly class IpcWorkerOutputCatcherFactory
{
    public function __construct(
        private BotLogFile $logFile,
        private StatusBroadcaster $statusBroadcaster,
        private LoggableLogger $loggableLogger,
    ) {
    }

    public function create(?API $api, Closure $ipcWorkerProcess): IpcWorkerOutputCatcher
    {
        return new IpcWorkerOutputCatcher(
            logPath: $this->logFile->path(),
            api: $api,
            statusBroadcaster: $this->statusBroadcaster,
            loggableLogger: $this->loggableLogger,
            ipcWorkerProcess: $ipcWorkerProcess,
        );
    }
}
