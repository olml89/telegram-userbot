<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Process;

enum ProcessType: string
{
    case RequestStatus = 'request-status';
    case PhoneLogin = 'phone-login';
    case CompletePhoneLogin = 'complete-phone-login';
    case Logout = 'logout';
    case Runner = 'runner';
}
