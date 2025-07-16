<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use olml89\TelegramUserbot\BotManager\Bot\Command\Command\CompletePhoneLoginCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\LogoutCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\PhoneLoginCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\RequestStatusCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\StartCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\StopCommand;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStartedException;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessNotStoppedException;
use olml89\TelegramUserbot\Shared\Bot\Status\InvalidStatusException;
use olml89\TelegramUserbot\Shared\Redis\RedisStorageException;
use Throwable;

interface CommandHandler
{
    public function broadcastStatus(?Throwable $e = null): void;

    /** @throws ProcessNotStartedException */
    public function requestStatus(RequestStatusCommand $command): void;

    /**
     * @throws InvalidStatusException
     * @throws ProcessNotStartedException
     */
    public function phoneLogin(PhoneLoginCommand $command): void;

    /**
     * @throws InvalidStatusException
     * @throws RedisStorageException
     * @throws ProcessNotStartedException
     */
    public function completePhoneLogin(CompletePhoneLoginCommand $command): void;

    /**
     * @throws InvalidStatusException
     * @throws ProcessNotStartedException
     */
    public function logout(LogoutCommand $command): void;

    /**
     * @throws InvalidStatusException
     * @throws ProcessNotStartedException
     */
    public function start(StartCommand $command): void;

    /**
     * @throws InvalidStatusException
     * @throws ProcessNotStoppedException
     */
    public function stop(StopCommand $command): void;
}
