<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotRuntime\Bot\Process;

enum ProcessResult: string
{
    case Started = 'started';
    case Stopped = 'stopped';
    case Running = 'running';
}
