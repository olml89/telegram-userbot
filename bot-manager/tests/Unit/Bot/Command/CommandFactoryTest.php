<?php

declare(strict_types=1);

namespace Test\BotManager\Unit\Bot\Command;

use JsonException;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\BroadcastStatusCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\CompletePhoneLoginCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\LogoutCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\PhoneLoginCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\StartCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\Command\StopCommand;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandFactory;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandType;
use olml89\TelegramUserbot\BotManager\Bot\Command\DisallowedCommandTypeException;
use olml89\TelegramUserbot\BotManager\Bot\Command\InvalidCommandTypeException;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(CommandFactory::class)]
final class CommandFactoryTest extends TestCase
{
    private CommandFactory $commandFactory;

    protected function setUp(): void
    {
        $this->commandFactory = new CommandFactory();
    }

    public function testItThrowsJsonExceptionIfJsonIsInvalid(): void
    {
        $json = '{type: "value"}';

        $this->expectException(JsonException::class);

        $this->commandFactory->fromJson($json);
    }

    public function testItThrowsInvalidCommandTypeExceptionIfTypeValueIsMissing(): void
    {
        $json = '{"property": "value"}';

        $this->expectExceptionObject(InvalidCommandTypeException::missingType());

        $this->commandFactory->fromJson($json);
    }

    public function testItThrowsInvalidCommandTypeExceptionIfTypeValueIsNotAString(): void
    {
        $json = '{"type": 1}';

        $this->expectExceptionObject(InvalidCommandTypeException::notString());

        $this->commandFactory->fromJson($json);
    }

    public function testItThrowsInvalidCommandTypeExceptionIfTypeValueIsNotValid(): void
    {
        $invalidCommandType = 'InvalidCommandType';
        $json = sprintf('{"type": "%s"}', $invalidCommandType);

        $this->expectExceptionObject(InvalidCommandTypeException::invalidType($invalidCommandType));

        $this->commandFactory->fromJson($json);
    }

    /**
     * @return array<int, array<int, CommandType>>
     */
    public static function provideDisallowedCommandTypes(): array
    {
        return [
            [CommandType::RequestStatus]
        ];
    }

    #[DataProvider('provideDisallowedCommandTypes')]
    public function testItThrowsDisallowedCommandTypeExceptionIfTypeValueIsNotAllowed(CommandType $disallowedType): void
    {
        $json = sprintf('{"type": "%s"}', $disallowedType->value);

        $this->expectExceptionObject(new DisallowedCommandTypeException($disallowedType));

        $this->commandFactory->fromJson($json);
    }

    /**
     * @return array<int, array<int, string|Command>>
     */
    public static function provideJsonAndExpectedCommand(): array
    {
        $phoneCode = new PhoneCode('12345');

        return [
            [
                sprintf('{"type": "%s"}', CommandType::BroadcastStatus->value),
                new BroadcastStatusCommand(),
            ],
            [
                sprintf('{"type": "%s"}', CommandType::PhoneLogin->value),
                new PhoneLoginCommand(),
            ],
            [
                sprintf('{"type": "%s", "code": "%s"}', CommandType::CompletePhoneLogin->value, $phoneCode),
                new CompletePhoneLoginCommand($phoneCode),
            ],
            [
                sprintf('{"type": "%s"}', CommandType::Logout->value),
                new LogoutCommand(),
            ],
            [
                sprintf('{"type": "%s"}', CommandType::Start->value),
                new StartCommand(),
            ],
            [
                sprintf('{"type": "%s"}', CommandType::Stop->value),
                new StopCommand(),
            ],
        ];
    }

    #[DataProvider('provideJsonAndExpectedCommand')]
    public function testItCreatesCommandFromJson(string $json, Command $expectedCommand): void
    {
        $command = $this->commandFactory->fromJson($json);

        self::assertEquals($expectedCommand, $command);
    }
}
