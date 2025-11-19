<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Process;

enum Process: string
{
    case RequestStatus = 'request-status';
    case PhoneLogin = 'phone-login';
    case CompletePhoneLogin = 'complete-phone-login';
    case Logout = 'logout';
    case Loop = 'loop';
}
