<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;
use olml89\TelegramUserbot\Application\Environment\Env;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandBus;
use olml89\TelegramUserbot\BotManager\Bot\Command\Handler\BroadcastStatusHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\Handler\CompletePhoneLoginHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\Handler\LogoutHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\Handler\PhoneLoginHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\Handler\RequestStatusHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\Handler\StartHandler;
use olml89\TelegramUserbot\BotManager\Bot\Command\Handler\StopHandler;
use olml89\TelegramUserbot\BotManager\Redis\ReactRedisSubscriber;
use olml89\TelegramUserbot\BotManager\Websocket\WebSocketServerConfig;
use olml89\TelegramUserbot\BotRuntime\App\EnvLoader;
use olml89\TelegramUserbot\BotRuntime\Error\SentryConfig;
use olml89\TelegramUserbot\BotRuntime\Redis\PhpRedis\PhpRedisPublisher;
use olml89\TelegramUserbot\BotRuntime\Redis\PhpRedis\PhpRedisStorage;
use olml89\TelegramUserbot\BotRuntime\Redis\RedisPublisher;
use olml89\TelegramUserbot\BotRuntime\Redis\RedisStorage;
use olml89\TelegramUserbot\BotRuntime\Redis\RedisSubscriber;

/**
 * Get the bot-runtime PHP-DI ContainerBuilder
 *
 * @var ContainerBuilder<Container> $containerBuilder
 */
$containerBuilder = require dirname(__DIR__, 2) . '/bot-runtime/bin/container-builder.php';

/**
 * Load bot-manager autoloader
 */
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Load bot-manager env vars and definitions
 */
$environment = EnvLoader::load(dirname(__DIR__));

$containerBuilder->addDefinitions([

    SentryConfig::class => DI\factory(
        fn(): SentryConfig => new SentryConfig(
            dsn: Env::string('BOT_MANAGER_SENTRY_DSN'),
        ),
    ),

    WebSocketServerConfig::class => DI\factory(
        fn(): WebSocketServerConfig => new WebSocketServerConfig(
            host: '0.0.0.0',
            port: 8080,
        ),
    ),

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

]);

return $containerBuilder->build();
