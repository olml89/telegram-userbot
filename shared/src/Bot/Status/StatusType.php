<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Status;

use olml89\TelegramUserbot\Shared\Bot\Status\Status\DisconnectedStatus;
use olml89\TelegramUserbot\Shared\Bot\Status\Status\LoggedInStatus;
use olml89\TelegramUserbot\Shared\Bot\Status\Status\LoggedOutStatus;
use olml89\TelegramUserbot\Shared\Bot\Status\Status\NotLoggedInStatus;
use olml89\TelegramUserbot\Shared\Bot\Status\Status\RunningStatus;
use olml89\TelegramUserbot\Shared\Bot\Status\Status\WaitingCodeStatus;
use olml89\TelegramUserbot\Shared\Bot\Status\Status\WaitingPasswordStatus;
use olml89\TelegramUserbot\Shared\Bot\Status\Status\WaitingSignupStatus;
use Stringable;

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

    public function status(null|string|Stringable $message, ?int $time = null): Status
    {
        return match ($this) {
            self::Disconnected => new DisconnectedStatus($message, $time),
            self::NotLoggedIn => new NotLoggedInStatus($message, $time),
            self::WaitingCode => new WaitingCodeStatus($message, $time),
            self::WaitingSignup => new WaitingSignupStatus($message, $time),
            self::WaitingPassword => new WaitingPasswordStatus($message, $time),
            self::LoggedIn => new LoggedInStatus($message, $time),
            self::LoggedOut => new LoggedOutStatus($message, $time),
            self::Running => new RunningStatus($message, $time),
        };
    }
}
