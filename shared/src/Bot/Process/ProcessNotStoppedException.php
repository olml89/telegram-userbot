<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Process;

use Exception;
use olml89\TelegramUserbot\Shared\App\ExecResult;

final class ProcessNotStoppedException extends Exception
{
    public function __construct(ProcessType $processType, ExecResult $result)
    {
        parent::__construct(
            sprintf(
                'Process %s not stopped on bot container: %s',
                $processType->value,
                $result,
            ),
        );
    }
}
