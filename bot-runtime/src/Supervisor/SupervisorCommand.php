<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotRuntime\Supervisor;

enum SupervisorCommand: string
{
    case Start = 'start';
    case Stop = 'stop';
    case Status = 'status';
}
