<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\App\Environment;

enum Environment: string
{
    case Development = 'development';
    case Production = 'production';
    case Testing = 'testing';
    case CI = 'ci';
}
