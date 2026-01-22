<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;
use Monolog\Level;
use olml89\TelegramUserbot\Shared\App\AppConfig;
use olml89\TelegramUserbot\Shared\App\Environment\Env;
use olml89\TelegramUserbot\Shared\App\Environment\Environment;
use olml89\TelegramUserbot\Shared\Bot\Process\ProcessManager;
use olml89\TelegramUserbot\Shared\Logger\LoggerConfig;
use olml89\TelegramUserbot\Shared\Redis\RedisConfig;
use olml89\TelegramUserbot\Shared\Supervisor\SupervisorConfig;
use olml89\TelegramUserbot\Shared\Supervisor\SupervisorCtl;

/**
 * Don't use an autoloader here, since this is not a service that is being run, this is always imported
 * by external services.
 *
 * Configurate the shared PHP-DI ContainerBuilder
 *
 * @var ContainerBuilder<Container> $containerBuilder
 */
$containerBuilder = new ContainerBuilder();
$containerBuilder->useAttributes(true);

/**
 * Load shared env vars and definitions
 */
Env::load(dirname(__DIR__));

return $containerBuilder->addDefinitions([

    AppConfig::class => DI\factory(function (): AppConfig {
        return new AppConfig(
            environment: Environment::load(Env::string('APP_ENV')),
        );
    }),

    LoggerConfig::class => DI\factory(function (): LoggerConfig {
        return new LoggerConfig(
            logDirectory: Env::string('LOG_DIRECTORY'),
            level: Level::tryFrom(Env::int('LOG_LEVEL')) ?? Level::Debug,
        );
    }),

    RedisConfig::class => DI\factory(function (): RedisConfig {
        return new RedisConfig(
            host: 'redis',
            statusChannel: Env::string('REDIS_STATUS_CHANNEL'),
            phoneCodeStorageKey: Env::string('REDIS_PHONE_CODE_STORAGE_KEY'),
        );
    }),

    SupervisorConfig::class => DI\factory(function (): SupervisorConfig {
        return new SupervisorConfig(
            configPath: Env::string('SUPERVISOR_CONFIG_PATH'),
        );
    }),

    ProcessManager::class => DI\autowire(SupervisorCtl::class)

]);
