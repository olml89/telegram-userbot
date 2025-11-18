<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Process;

interface ProcessManager
{
    /** @throws ProcessNotStartedException */
    public function start(ProcessType $processType): ProcessResult;

    /** @throws ProcessNotStoppedException */
    public function stop(ProcessType $processType): ProcessResult;

    public function isRunning(ProcessType $processType): bool;
}
