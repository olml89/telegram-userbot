<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Logger;

enum Channel: string
{
    # Jobs without service
    case Monolog = 'monolog';

    # Jobs from the bot-manager service
    case Command = 'bot-manager/command';
    case Status = 'bot-manager/status';
    case WebSocketServer = 'bot-manager/websocket-server';

    # Jobs from the bot service
    case Supervisord = 'bot/supervisord';
    case RequestStatus = 'bot/request-status';
    case PhoneLogin = 'bot/phone-login';
    case CompletePhoneLogin = 'bot/complete-phone-login';
    case Logout = 'bot/logout';
    case Runner = 'bot/runner';

    public function logFilePath(LoggerConfig $config): string
    {
        return sprintf('%s/%s.log', $config->logDirectory, $this->value);
    }
}
