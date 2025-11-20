<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\MadelineProto;

use danog\MadelineProto\API;
use olml89\TelegramUserbot\Shared\Bot\Process\Process;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessManager;
use olml89\TelegramUserbot\Shared\Bot\Status\Status;
use olml89\TelegramUserbot\Shared\Bot\Status\StatusType;

final readonly class ApiStatusCalculator
{
    public function __construct(
        private ProcessManager $processManager,
    ) {
    }

    public function calculate(?API $api): Status
    {
        return new Status($this->calculateStatusType($api));
    }

    private function calculateStatusType(?API $api): StatusType
    {
        if (is_null($api)) {
            return StatusType::Disconnected;
        }

        $authorization = $api->getAuthorization();

        if ($authorization === API::LOGGED_IN) {
            return $this->processManager->isRunning(Process::Loop)
                ? StatusType::Running
                : StatusType::LoggedIn;
        }

        return match ($authorization) {
            API::NOT_LOGGED_IN => StatusType::NotLoggedIn,
            API::WAITING_CODE => StatusType::WaitingCode,
            API::WAITING_SIGNUP => StatusType::WaitingSignup,
            API::WAITING_PASSWORD => StatusType::WaitingPassword,
            API::LOGGED_OUT => StatusType::LoggedOut,
        };
    }
}
