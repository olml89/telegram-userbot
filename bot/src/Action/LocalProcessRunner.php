<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

use olml89\TelegramUserbot\Shared\Bot\Process\ProcessType;

final readonly class LocalProcessRunner
{
    private const string ASYNC_PHP =  '/usr/local/bin/php %s > /dev/null 2>&1 &';
    private const string ACTION_SCRIPT_FILE = '/telegram-userbot/bot/bin/action/%s.php';

    public function run(ProcessType $processType): void
    {
        $actionScriptFile = sprintf(self::ACTION_SCRIPT_FILE, $processType->value);
        $cmd = sprintf(self::ASYNC_PHP, $actionScriptFile);

        exec(command: $cmd);
    }
}
