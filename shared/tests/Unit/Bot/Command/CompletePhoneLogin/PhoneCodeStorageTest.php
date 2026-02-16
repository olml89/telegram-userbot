<?php

declare(strict_types=1);

namespace Test\Shared\Unit\Bot\Command\CompletePhoneLogin;

use Mockery\MockInterface;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\LogRecord\DeletedPhoneCode;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\LogRecord\RetrievedPhoneCode;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\LogRecord\StoredPhoneCode;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCode;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCodeStorage;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use olml89\TelegramUserbot\Shared\Redis\RedisConfig;
use olml89\TelegramUserbot\Shared\Redis\RedisStorage;
use olml89\TelegramUserbot\Shared\Redis\RedisStorageException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Test\Shared\Unit\UsesMockery;

#[CoversClass(PhoneCodeStorage::class)]
final class PhoneCodeStorageTest extends TestCase
{
    use UsesMockery;

    private RedisConfig $redisConfig;

    protected function setUp(): void
    {
        $this->redisConfig = new RedisConfig(
            host: 'host',
            statusChannel: 'status',
            phoneCodeStorageKey: 'phoneCodeStorageKey',
        );
    }

    private function createPhoneCodeStorage(RedisStorage $redisStorage, LoggableLogger $loggableLogger): PhoneCodeStorage
    {
        return new PhoneCodeStorage(
            $this->redisConfig,
            $redisStorage,
            $loggableLogger,
        );
    }

    public function testItThrowsRedisStorageExceptionIfItCannotGetTheValueFromRedis(): void
    {
        $expectedException = RedisStorageException::get($this->redisConfig->phoneCodeStorageKey);

        /** @var RedisStorage&MockInterface $redisStorage */
        $redisStorage = $this->mockeryMock(RedisStorage::class);

        /** @var LoggableLogger&MockInterface $loggableLogger */
        $loggableLogger = $this->mockeryMock(LoggableLogger::class);

        $redisStorage
            ->shouldReceive('get')
            ->once()
            ->with($this->redisConfig->phoneCodeStorageKey);

        $phoneCodeStorage = $this->createPhoneCodeStorage($redisStorage, $loggableLogger);

        $this->expectExceptionObject($expectedException);

        $phoneCodeStorage->retrieve();
    }

    public function testItThrowsRedisStorageExceptionIfItCannotDeleteTheKeyFromRedis(): void
    {
        $phoneCodeValue = '12345';
        $phoneCode = new PhoneCode($phoneCodeValue);
        $expectedException = RedisStorageException::get($this->redisConfig->phoneCodeStorageKey);

        /** @var RedisStorage&MockInterface $redisStorage */
        $redisStorage = $this->mockeryMock(RedisStorage::class);

        /** @var LoggableLogger&MockInterface $loggableLogger */
        $loggableLogger = $this->mockeryMock(LoggableLogger::class);

        $redisStorage
            ->shouldReceive('get')
            ->once()
            ->with($this->redisConfig->phoneCodeStorageKey)
            ->andReturn($phoneCode);

        $loggableLogger
            ->shouldReceive('log')
            ->once()
            ->with(
                $this->expectedArgument(new RetrievedPhoneCode($phoneCode, $this->redisConfig->phoneCodeStorageKey)),
            );

        $redisStorage
            ->shouldReceive('del')
            ->once()
            ->with($this->redisConfig->phoneCodeStorageKey)
            ->andThrow($expectedException);

        $phoneCodeStorage = $this->createPhoneCodeStorage($redisStorage, $loggableLogger);

        $this->expectExceptionObject($expectedException);

        $phoneCodeStorage->retrieve();
    }

    public function testItRetrievesThePhoneCode(): void
    {
        $phoneCodeValue = '12345';
        $phoneCode = new PhoneCode($phoneCodeValue);

        /** @var RedisStorage&MockInterface $redisStorage */
        $redisStorage = $this->mockeryMock(RedisStorage::class);

        /** @var LoggableLogger&MockInterface $loggableLogger */
        $loggableLogger = $this->mockeryMock(LoggableLogger::class);

        $redisStorage
            ->shouldReceive('get')
            ->once()
            ->ordered()
            ->with($this->redisConfig->phoneCodeStorageKey)
            ->andReturn($phoneCodeValue);

        $loggableLogger
            ->shouldReceive('log')
            ->once()
            ->ordered()
            ->with(
                $this->expectedArgument(new RetrievedPhoneCode($phoneCode, $this->redisConfig->phoneCodeStorageKey)),
            );

        $redisStorage
            ->shouldReceive('del')
            ->once()
            ->ordered()
            ->with($this->redisConfig->phoneCodeStorageKey);

        $loggableLogger
            ->shouldReceive('log')
            ->once()
            ->ordered()
            ->with(
                $this->expectedArgument(new DeletedPhoneCode($phoneCode, $this->redisConfig->phoneCodeStorageKey)),
            );

        $phoneCodeStorage = $this->createPhoneCodeStorage($redisStorage, $loggableLogger);

        $phoneCodeStorage->retrieve();
    }

    public function testItThrowsRedisStorageExceptionIfItCannotStoreTheValueToRedis(): void
    {
        $phoneCodeValue = '12345';
        $phoneCode = new PhoneCode($phoneCodeValue);
        $expectedException = RedisStorageException::set($this->redisConfig->phoneCodeStorageKey);

        /** @var RedisStorage&MockInterface $redisStorage */
        $redisStorage = $this->mockeryMock(RedisStorage::class);

        /** @var LoggableLogger&MockInterface $loggableLogger */
        $loggableLogger = $this->mockeryMock(LoggableLogger::class);

        $redisStorage
            ->shouldReceive('set')
            ->once()
            ->with(
                $this->redisConfig->phoneCodeStorageKey,
                $phoneCodeValue,
            )
            ->andThrow($expectedException);

        $phoneCodeStorage = $this->createPhoneCodeStorage($redisStorage, $loggableLogger);

        $this->expectExceptionObject($expectedException);

        $phoneCodeStorage->store($phoneCode);
    }

    public function testItStoresThePhoneCode(): void
    {
        $phoneCodeValue = '12345';
        $phoneCode = new PhoneCode($phoneCodeValue);

        /** @var RedisStorage&MockInterface $redisStorage */
        $redisStorage = $this->mockeryMock(RedisStorage::class);

        /** @var LoggableLogger&MockInterface $loggableLogger */
        $loggableLogger = $this->mockeryMock(LoggableLogger::class);

        $redisStorage
            ->shouldReceive('set')
            ->once()
            ->with(
                $this->redisConfig->phoneCodeStorageKey,
                $phoneCodeValue,
            );

        $loggableLogger
            ->shouldReceive('log')
            ->once()
            ->with(
                $this->expectedArgument(new StoredPhoneCode($phoneCode, $this->redisConfig->phoneCodeStorageKey)),
            );

        $phoneCodeStorage = $this->createPhoneCodeStorage($redisStorage, $loggableLogger);

        $phoneCodeStorage->store($phoneCode);
    }
}
