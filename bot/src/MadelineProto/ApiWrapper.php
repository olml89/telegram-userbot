<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\MadelineProto;

use danog\MadelineProto\API;
use danog\MadelineProto\EventHandler;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCode;
use olml89\TelegramUserbot\Shared\Bot\Status\Status;

final class ApiWrapper
{
    private ?API $api = null;

    public function __construct(
        private readonly ApiStatusCalculator $apiStatusCalculator,
    ) {
    }

    public function status(): Status
    {
        return $this->apiStatusCalculator->calculate($this->api);
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
}
