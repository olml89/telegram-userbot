<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Command;

use JsonException;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\BroadcastStatusCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\CompletePhoneLoginCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\LogoutCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\PhoneLoginCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\StartCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\StopCommand;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\InvalidPhoneCodeException;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCode;

final readonly class CommandFactory
{
    /**
     * @throws JsonException
     * @throws InvalidCommandTypeException
     * @throws InvalidPhoneCodeException
     * @throws DisallowedCommandTypeException
     */
    public function fromJson(string $json): Command
    {
        /** @var array<string, mixed> $data */
        $data = json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR);
        $typeValue = $data['type'] ?? null;

        if (is_null($typeValue)) {
            throw InvalidCommandTypeException::missingType();
        }

        if (!is_string($typeValue)) {
            throw InvalidCommandTypeException::notString();
        }

        if (is_null($commandType = CommandType::tryFrom($typeValue))) {
            throw InvalidCommandTypeException::invalidType($typeValue);
        }

        return match ($commandType) {
            CommandType::BroadcastStatus => new BroadcastStatusCommand(),
            CommandType::PhoneLogin => new PhoneLoginCommand(),
            CommandType::CompletePhoneLogin => new CompletePhoneLoginCommand(new PhoneCode($data['code'] ?? null)),
            CommandType::Logout => new LogoutCommand(),
            CommandType::Start => new StartCommand(),
            CommandType::Stop => new StopCommand(),
            default => throw new DisallowedCommandTypeException($commandType),
        };
    }
}
