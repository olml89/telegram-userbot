<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandBus;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandRunner;
use olml89\TelegramUserbot\BotManager\Bot\Command\Handler\BroadcastStatusHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\Handler\CompletePhoneLoginHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\Handler\LogoutHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\Handler\PhoneLoginHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\Handler\RequestStatusHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\Handler\StartHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\Handler\StopHandler;
use olml89\TelegramUserbot\BotManager\Bot\Status\StatusInitializer;
use olml89\TelegramUserbot\BotManager\Bot\Status\StatusManager;
use olml89\TelegramUserbot\BotManager\Bot\Status\StatusSubscriber;
use olml89\TelegramUserbot\BotManager\Redis\ReactRedisSubscriber;
use olml89\TelegramUserbot\BotManager\Websocket\WebSocketConnectionPool;
use olml89\TelegramUserbot\BotManager\Websocket\WebSocketServer;
use olml89\TelegramUserbot\BotManager\Websocket\WebSocketServerConfig;
use olml89\TelegramUserbot\Shared\App\Environment\Env;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCodeStorage;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessManager;
use olml89\TelegramUserbot\Shared\Logger\Channel;
use olml89\TelegramUserbot\Shared\Logger\LogRecordLogger;
use olml89\TelegramUserbot\Shared\Logger\LogRecordLoggerFactory;
use olml89\TelegramUserbot\Shared\Redis\PhpRedis\PhpRedisPublisher;
use olml89\TelegramUserbot\Shared\Redis\PhpRedis\PhpRedisStorage;
use olml89\TelegramUserbot\Shared\Redis\RedisPublisher;
use olml89\TelegramUserbot\Shared\Redis\RedisStorage;
use olml89\TelegramUserbot\Shared\Redis\RedisSubscriber;
use olml89\TelegramUserbot\Shared\Supervisor\SupervisorCtl;

/**
 * Load bot-manager autoloader
 */
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Get the shared PHP-DI ContainerBuilder
 *
 * @var ContainerBuilder<Container> $containerBuilder
 */
$containerBuilder = require dirname(__DIR__, 2) . '/shared/bin/container-builder.php';

/**
 * Load bot-manager env vars and definitions
 */
Env::load(dirname(__DIR__));

$containerBuilder->addDefinitions([

    WebSocketServerConfig::class => DI\factory(function (): WebSocketServerConfig {
        return new WebSocketServerConfig(
            host: '0.0.0.0',
            port: 8080,
        );
    }),

    CommandBus::class => DI\autowire()->constructor([
        DI\autowire(BroadcastStatusHandler::class),
        DI\autowire(CompletePhoneLoginHandler::class),
        DI\autowire(LogoutHandler::class),
        DI\autowire(PhoneLoginHandler::class),
        DI\autowire(RequestStatusHandler::class),
        DI\autowire(StartHandler::class),
        DI\autowire(StopHandler::class),
    ]),

    RedisPublisher::class => DI\autowire(PhpRedisPublisher::class),
    RedisSubscriber::class => DI\autowire(ReactRedisSubscriber::class),
    RedisStorage::class => DI\autowire(PhpRedisStorage::class),

    Channel::Status->value => DI\factory(function (Container $c): LogRecordLogger {
        /** @var LogRecordLoggerFactory $logRecordLoggerFactory */
        $logRecordLoggerFactory = $c->get(LogRecordLoggerFactory::class);

        return $logRecordLoggerFactory->create(Channel::Status);
    }),

    StatusInitializer::class => DI\autowire()->constructorParameter(
        'loggableLogger',
        DI\get(Channel::Status->value),
    ),

    StatusManager::class => DI\autowire()->constructorParameter(
        'loggableLogger',
        DI\get(Channel::Status->value),
    ),

    StatusSubscriber::class => DI\autowire()->constructorParameter(
        'loggableLogger',
        DI\get(Channel::Status->value),
    ),

    Channel::WebSocketServer->value => DI\factory(function (Container $c): LogRecordLogger {
        /** @var LogRecordLoggerFactory $logRecordLoggerFactory */
        $logRecordLoggerFactory = $c->get(LogRecordLoggerFactory::class);

        return $logRecordLoggerFactory->create(Channel::WebSocketServer);
    }),

    WebSocketConnectionPool::class => DI\autowire()->constructorParameter(
        'loggableLogger',
        DI\get(Channel::WebSocketServer->value),
    ),

    WebSocketServer::class => DI\autowire()->constructorParameter(
        'loggableLogger',
        DI\get(Channel::WebSocketServer->value),
    ),

    Channel::Command->value => DI\factory(function (Container $c): LogRecordLogger {
        /** @var LogRecordLoggerFactory $logRecordLoggerFactory */
        $logRecordLoggerFactory = $c->get(LogRecordLoggerFactory::class);

        return $logRecordLoggerFactory->create(Channel::Command);
    }),

    ProcessManager::class => DI\autowire(SupervisorCtl::class)->constructorParameter(
        'loggableLogger',
        DI\get(Channel::Command->value),
    ),

    CommandRunner::class => DI\autowire()->constructorParameter(
        'loggableLogger',
        DI\get(Channel::Command->value),
    ),

    PhoneCodeStorage::class => DI\autowire()->constructorParameter(
        'loggableLogger',
        DI\get(Channel::Command->value),
    ),

]);

return $containerBuilder->build();
