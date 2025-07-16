<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Process;

interface ProcessManager
{
    /** @throws ProcessNotStartedException */
    public function start(Process $process): ProcessResult;

    /** @throws ProcessNotStoppedException */
    public function stop(Process $process): ProcessResult;

    public function isRunning(Process $process): bool;
}
