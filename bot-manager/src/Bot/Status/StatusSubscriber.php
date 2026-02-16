<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Bot\Status;

use JsonException;
use olml89\TelegramUserbot\BotManager\Bot\Status\LogRecord\SubscribedToChannel;
use olml89\TelegramUserbot\Shared\Bot\Status\InvalidStatusTypeException;
use olml89\TelegramUserbot\Shared\Bot\Status\StatusFactory;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\ErrorLogRecord;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use olml89\TelegramUserbot\Shared\Redis\RedisConfig;
use olml89\TelegramUserbot\Shared\Redis\RedisSubscriber;

/**
 * It subscribes to Status updates from Redis, published by a StatusEmitter
 */
final readonly class StatusSubscriber
{
    public function __construct(
        private RedisConfig $config,
        private RedisSubscriber $subscriber,
        private StatusFactory $statusFactory,
        private LoggableLogger $loggableLogger,
    ) {}

    public function subscribe(StatusManager $statusManager): void
    {
        $this->subscriber->subscribe(
            $this->config->statusChannel,
            fn(string $message) => $this->processMessage($message, $statusManager),
        );

        $this->loggableLogger->log(new SubscribedToChannel($this->config->statusChannel));
    }

    private function processMessage(string $message, StatusManager $statusManager): void
    {
        try {
            $status = $this->statusFactory->fromJson($message);
            $statusManager->record($status);
        } catch (JsonException|InvalidStatusTypeException $e) {
            $this->loggableLogger->log(new ErrorLogRecord('Invalid status', $e));
        }
    }
}
