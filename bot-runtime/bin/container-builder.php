<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;
use olml89\TelegramUserbot\Application\Environment\Env;
use olml89\TelegramUserbot\BotRuntime\App\AppConfig;
use olml89\TelegramUserbot\BotRuntime\App\EnvLoader;
use olml89\TelegramUserbot\BotRuntime\Bot\Process\ProcessManager;
use olml89\TelegramUserbot\BotRuntime\Logger\LogRecord\ErrorLogRecord;
use olml89\TelegramUserbot\BotRuntime\Logger\LogRecord\LoggableLogger;
use olml89\TelegramUserbot\BotRuntime\Logger\LogRecord\WarningLogRecord;
use olml89\TelegramUserbot\BotRuntime\Logger\LogRecordLoggerFactory;
use olml89\TelegramUserbot\BotRuntime\Redis\RedisConfig;
use olml89\TelegramUserbot\BotRuntime\Supervisor\SupervisorConfig;
use olml89\TelegramUserbot\BotRuntime\Supervisor\SupervisorCtl;

/**
 * Load bot-runtime autoloader
 */
require dirname(__DIR__) . '/vendor/autoload.php';
$loggableLogger = LogRecordLoggerFactory::create();

/**
 * Convert PHP errors (deprecated notices, warnings, errors) to exceptions.
 *
 * This allows errors to be caught by try/catch blocks and ensures they are
 * properly logged through the exception handler.
 *
 * Note: This does NOT catch Throwable exceptions that have already been thrown—only traditional PHP errors.
 */
set_error_handler(
    static function (int $errno, string $errstr, string $errfile, int $errline) use ($loggableLogger): bool {
        $exception = new ErrorException($errstr, 0, $errno, $errfile, $errline);

        /**
         * Ignore deprecation warnings from vendor libraries
         */
        if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED) {
            $warningLogRecord = new WarningLogRecord('Deprecation warning', $exception);
            $loggableLogger->log($warningLogRecord);

            return true;
        }

        throw $exception;
    },
);

/**
 * Uncaught exceptions handler that writes directly to stdout.
 *
 * Why stdout and not stderr?
 * - error_log(), fwrite(STDERR), and php://stderr don't work reliably in this environment
 *   because Supervisor has already redirected file descriptors before PHP writes to them.
 * - echo writes directly to FD 1 (stdout) at the PHP level, bypassing these redirection issues.
 * - Docker and Supervisor both capture stdout/stderr identically, so using stdout ensures
 *   the exception is always captured and sent to Alloy/Loki.
 *
 * The stderr_logfile=/dev/stderr in supervisord.conf is kept for consistency with the
 * convention of routing error-level logs to stderr (used by Monolog handlers for regular logging).
 * However, uncaught exceptions require this direct stdout approach to guarantee delivery.
 *
 * @see LogRecordLoggerFactory for how regular errors/warnings are logged to stderr via Monolog
 */
set_exception_handler(
    static function (Throwable $e) use ($loggableLogger): never {
        $uncaughtLogError = new ErrorLogRecord('Uncaught exception', $e);
        $loggableLogger->log($uncaughtLogError);

        exit(1);
    },
);

/**
 * Configurate the bot-runtime PHP-DI ContainerBuilder
 *
 * @var ContainerBuilder<Container> $containerBuilder
 */
$containerBuilder = new ContainerBuilder();
$containerBuilder->useAttributes(true);

/**
 * Load shared env vars and definitions
 */
$environment = EnvLoader::load(dirname(__DIR__));

return $containerBuilder->addDefinitions([

    LoggableLogger::class => DI\value($loggableLogger),

    AppConfig::class => DI\factory(
        fn(): AppConfig => new AppConfig(
            environment: $environment,
        ),
    ),

    RedisConfig::class => DI\factory(
        fn(): RedisConfig => new RedisConfig(
            host: 'redis',
            statusChannel: Env::string('REDIS_STATUS_CHANNEL'),
            phoneCodeStorageKey: Env::string('REDIS_PHONE_CODE_STORAGE_KEY'),
        ),
    ),

    SupervisorConfig::class => DI\factory(
        fn(): SupervisorConfig => new SupervisorConfig(
            configPath: Env::string('SUPERVISOR_CONFIG_PATH'),
        ),
    ),

    ProcessManager::class => DI\autowire(SupervisorCtl::class),

]);
