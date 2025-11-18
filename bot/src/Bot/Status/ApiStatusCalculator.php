<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\Bot\Status;

use danog\MadelineProto\API;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessType;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessManager;
use olml89\TelegramUserbot\Shared\Bot\Status\Status;
use olml89\TelegramUserbot\Shared\Bot\Status\StatusType;

/**
 * It calculates the Status based on the MadelineProto API status (API::getAuthorization())
 */
final readonly class ApiStatusCalculator
{
    public function __construct(
        private ProcessManager $processManager,
    ) {
    }

    public function calculate(?Api $api): Status
    {
        return new Status($this->calculateStatusTye($api));
    }

    private function calculateStatusTye(?API $api): StatusType
    {
        if (is_null($api)) {
            return StatusType::Disconnected;
        }

        $authorization = $api->getAuthorization();

        if ($authorization === API::LOGGED_IN) {
            return $this->processManager->isRunning(ProcessType::Runner)
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
