<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;
use olml89\TelegramUserbot\Application\Environment\Env;
use olml89\TelegramUserbot\Bot\Bot\BotConfig;
use olml89\TelegramUserbot\Bot\Bot\BotLogFile;
use olml89\TelegramUserbot\Bot\Bot\BotSession;
use olml89\TelegramUserbot\Bot\Redis\AmphpRedisPublisher;
use olml89\TelegramUserbot\BotRuntime\App\EnvLoader;
use olml89\TelegramUserbot\BotRuntime\Error\SentryConfig;
use olml89\TelegramUserbot\BotRuntime\Redis\PhpRedis\PhpRedisStorage;
use olml89\TelegramUserbot\BotRuntime\Redis\RedisPublisher;
use olml89\TelegramUserbot\BotRuntime\Redis\RedisStorage;

/**
 * Get the bot-runtime PHP-DI ContainerBuilder
 *
 * @var ContainerBuilder<Container> $containerBuilder
 */
$containerBuilder = require dirname(__DIR__, 2) . '/bot-runtime/bin/container-builder.php';

/**
 * Load bot autoloader
 */
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Load bot env vars and definitions
 */
$environment = EnvLoader::load(dirname(__DIR__));

$containerBuilder->addDefinitions([

    SentryConfig::class => DI\factory(
        fn(): SentryConfig => new SentryConfig(
            dsn: Env::string('BOT_SENTRY_DSN'),
        ),
    ),

    BotConfig::class => DI\factory(
        fn(): BotConfig => new BotConfig(
            apiId: Env::int('TELEGRAM_API_ID'),
            apiHash: Env::string('TELEGRAM_API_HASH'),
            phoneNumber: Env::string('TELEGRAM_PHONE_NUMBER'),
            username: Env::string('TELEGRAM_USERNAME'),
        ),
    ),

    BotSession::class => DI\factory(
        fn(): BotSession => new BotSession(
            path: '/telegram-userbot/bot/var/madeline.session',
        ),
    ),

    BotLogFile::class => DI\factory(
        fn(): BotLogFile => new BotLogFile(
            path: '/telegram-userbot/bot/MadelineProto.log',
        ),
    ),

    RedisPublisher::class => DI\autowire(AmphpRedisPublisher::class),
    RedisStorage::class => DI\autowire(PhpRedisStorage::class),

]);

return $containerBuilder->build();
