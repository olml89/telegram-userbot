<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Action;

interface Action
{
    public function run(): void;
}
