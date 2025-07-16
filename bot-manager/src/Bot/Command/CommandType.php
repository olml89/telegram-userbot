<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

enum CommandType: string
{
    case BroadcastStatus = 'BroadcastStatus';
    case RequestStatus = 'RequestStatus';
    case PhoneLogin = 'PhoneLogin';
    case CompletePhoneLogin = 'CompletePhoneLogin';
    case Logout = 'Logout';
    case Start = 'Start';
    case Stop = 'Stop';
}
