<?php

declare(strict_types=1);

namespace Test\Bot\Unit\Output;

use olml89\TelegramUserbot\Bot\Output\MadelineProtoFileLoggerOutput;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Stringable;

#[CoversClass(MadelineProtoFileLoggerOutput::class)]
final class MadelineProtoFileLoggerOutputTest extends TestCase
{
    public function testIsBroadcastableReturnsFalseIfLineIsEmpty(): void
    {
        $madelineProtoFileLoggerOutput = new MadelineProtoFileLoggerOutput('');

        self::assertFalse($madelineProtoFileLoggerOutput->isBroadcastable());
    }

    /**
     * @return array<string[]>
     */
    public static function provideUndesiredChannels(): array
    {
        return [
            ['APIWrapper'],
            ['PingLoop'],
            ['Session'],
            ['SessionPaths'],
        ];
    }

    #[DataProvider('provideUndesiredChannels')]
    public function testIsBroadcastableReturnsFalseIfLineBelongsToUndesiredChannel(string $channel): void
    {
        $madelineProtoFileLoggerOutput = new MadelineProtoFileLoggerOutput($channel . ':');

        self::assertFalse($madelineProtoFileLoggerOutput->isBroadcastable());
    }

    public function testIsBroadcastableReturnsTrueIfLineIsNotEmptyAndFromADesiredChannel(): void
    {
        $madelineProtoFileLoggerOutput = new MadelineProtoFileLoggerOutput('DesiredChannel:');

        self::assertTrue($madelineProtoFileLoggerOutput->isBroadcastable());
    }

    public function testItThrowsRuntimeExceptionIfItTriesToStringifyWhenItIsNotBroadcastable(): void
    {
        $line = '';
        $madelineProtoFileLoggerOutput = new MadelineProtoFileLoggerOutput($line);

        $this->expectExceptionObject(
            new RuntimeException(sprintf('Output not broadcastable: %s', $line)),
        );

        $madelineProtoFileLoggerOutput->__toString();
    }

    public function testItStringifiesTheWholeLineIfItDoesNotContainTabCharacter(): void
    {
        $line = 'Line without tabs';
        $madelineProtoFileLoggerOutput = new MadelineProtoFileLoggerOutput($line);

        self::assertEquals($line, (string)$madelineProtoFileLoggerOutput);
    }

    public function testItStringifiesTheWholeLineIfItTabCharacterCountIsBiggerThanOne(): void
    {
        $line = "Channel:\tLine\twith\tmultiple\ttabs";
        $madelineProtoFileLoggerOutput = new MadelineProtoFileLoggerOutput($line);

        self::assertEquals($line, (string)$madelineProtoFileLoggerOutput);
    }

    public function testItStringifiesStrippingTabCharacterIfItHasASingleTabCharacter(): void
    {
        $line = "Channel:\tLine with a single tab";
        $madelineProtoFileLoggerOutput = new MadelineProtoFileLoggerOutput($line);

        self::assertEquals('Line with a single tab', (string)$madelineProtoFileLoggerOutput);
    }

    /**
     * @return array<int, array<int, string|Stringable>>
     */
    public static function provideLine(): array
    {
        $expected = 'Line with ending and beginning spaces';
        $line = "\t \t$expected\t \t";

        return [
            [
                $expected,
                $line,
            ],
            [
                $expected,
                new readonly class ($line) implements Stringable {
                    public function __construct(
                        private string $value,
                    ) {
                    }

                    public function __toString(): string
                    {
                        return $this->value;
                    }
                },
            ],
        ];
    }

    #[DataProvider('provideLine')]
    public function testItStringifiesTrimmingTheLine(string $expected, string|Stringable $line): void
    {
        $madelineProtoFileLoggerOutput = new MadelineProtoFileLoggerOutput($line);

        self::assertEquals($expected, (string)$madelineProtoFileLoggerOutput);
    }
}
