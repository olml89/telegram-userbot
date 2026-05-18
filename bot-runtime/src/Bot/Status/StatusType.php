<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotRuntime\Bot\Status;

enum StatusType: string
{
    case Disconnected = 'Disconnected';
    case NotLoggedIn = 'NotLoggedIn';
    case WaitingCode = 'WaitingCode';
    case WaitingSignup = 'WaitingSignup';
    case WaitingPassword = 'WaitingPassword';
    case LoggedIn = 'LoggedIn';
    case LoggedOut = 'LoggedOut';
    case Running = 'Running';
}
