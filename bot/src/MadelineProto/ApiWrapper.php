<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\MadelineProto;

use danog\MadelineProto\API;
use danog\MadelineProto\EventHandler;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCode;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessManager;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessType;
use olml89\TelegramUserbot\Shared\Bot\Status\Status;
use olml89\TelegramUserbot\Shared\Bot\Status\StatusType;

final class ApiWrapper
{
    private ?API $api = null;

    public function __construct(
        private readonly ProcessManager $processManager,
    ) {
    }

    /**
     * @throws ApiInitializationException
     */
    public function set(API $api): void
    {
        if (!is_null($this->api)) {
            throw ApiInitializationException::alreadyInitialized();
        }

        $this->api = $api;
    }

    /**
     * @throws ApiInitializationException
     */
    private function api(): API
    {
        if (is_null($this->api)) {
            throw ApiInitializationException::notInitialized();
        }

        return $this->api;
    }

    /**
     * @throws ApiInitializationException
     */
    public function phoneLogin(string $phoneNumber): void
    {
        $this->api()->phoneLogin($phoneNumber);
    }

    /**
     * @throws ApiInitializationException
     */
    public function completePhoneLogin(PhoneCode $phoneCode): void
    {
        $this->api()->completePhoneLogin((string)$phoneCode);
    }

    /**
     * @throws ApiInitializationException
     */
    public function logout(): void
    {
        $this->api()->logout();
    }

    /**
     * @param class-string<EventHandler> $eventHandlerClass
     *
     * @throws ApiInitializationException
     */
    public function startLoop(string $eventHandlerClass): void
    {
        API::startAndLoopMulti([$this->api()], $eventHandlerClass);
    }

    public function status(): Status
    {
        return new Status($this->calculateStatusType($this->api));
    }

    public function calculateStatusType(?API $api): StatusType
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
