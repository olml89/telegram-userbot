<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;
use olml89\TelegramUserbot\Bot\Bot\BotConfig;
use olml89\TelegramUserbot\Bot\Bot\BotLogFile;
use olml89\TelegramUserbot\Bot\Bot\BotSession;
use olml89\TelegramUserbot\Shared\App\Environment\Env;
use olml89\TelegramUserbot\Shared\Redis\PhpRedis\PhpRedisPublisher;
use olml89\TelegramUserbot\Shared\Redis\PhpRedis\PhpRedisStorage;
use olml89\TelegramUserbot\Shared\Redis\RedisPublisher;
use olml89\TelegramUserbot\Shared\Redis\RedisStorage;

require dirname(__DIR__).'/vendor/autoload.php';

/**
 * Get the shared PHP-DI ContainerBuilder
 *
 * @var ContainerBuilder<Container> $containerBuilder
 */
$containerBuilder = require dirname(__DIR__, 2).'/shared/bin/container-builder.php';

/**
 * Load bot env vars and definitions
 */
Env::load(dirname(__DIR__));

return $containerBuilder->addDefinitions([

    BotConfig::class => DI\factory(function (): BotConfig {
        return new BotConfig(
            apiId: Env::int('TELEGRAM_API_ID'),
            apiHash: Env::string('TELEGRAM_API_HASH'),
            phoneNumber: Env::string('TELEGRAM_PHONE_NUMBER'),
            username: Env::string('TELEGRAM_USERNAME'),
        );
    }),

    BotSession::class => DI\factory(function (): BotSession {
        return new BotSession(
            path: '/telegram-userbot/bot/var/madeline.session',
        );
    }),

    BotLogFile::class => DI\factory(function (): BotLogFile {
        return new BotLogFile(
            path: '/telegram-userbot/bot/MadelineProto.log',
        );
    }),

    RedisPublisher::class => DI\autowire(PhpRedisPublisher::class),
    RedisStorage::class => DI\autowire(PhpRedisStorage::class),

]);
