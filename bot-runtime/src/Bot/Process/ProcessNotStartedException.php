<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotRuntime\Bot\Process;

use Exception;
use olml89\TelegramUserbot\BotRuntime\App\ExecResult;

final class ProcessNotStartedException extends Exception
{
    public function __construct(Process $process, ExecResult $result)
    {
        parent::__construct(
            sprintf(
                'Process %s not started on bot container: %s',
                $process->value,
                $result,
            ),
        );
    }
}
