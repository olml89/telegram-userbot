<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotRuntime\Logger;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Throwable;

final class LogRecordLoggerFactory
{
    public static function create(): LogRecordLogger
    {
        $logger = new Logger('bot-runtime');

        $logger->setExceptionHandler(
            static function (Throwable $e): void {
                echo ExceptionEncoder::encode($e) . PHP_EOL;
            },
        );

        $appHandler = new StreamHandler(
            stream: 'php://stdout',
            level: Level::Debug,
            bubble: false,
        );
        $appHandler->setFormatter(new JsonFormatter(
            ignoreEmptyContextAndExtra: true,
            includeStacktraces: false,
        ));
        $logger->pushHandler($appHandler);

        $warningsHandler = new StreamHandler(
            stream: 'php://stderr',
            level: Level::Warning,
            bubble: false,
        );
        $warningsHandler->setFormatter(new JsonFormatter(
            ignoreEmptyContextAndExtra: true,
            includeStacktraces: false,
        ));
        $logger->pushHandler($warningsHandler);

        $errorsHandler = new StreamHandler(
            stream: 'php://stderr',
            level: Level::Error,
            bubble: false,
        );
        $errorsHandler->setFormatter(new JsonFormatter(
            ignoreEmptyContextAndExtra: true,
            includeStacktraces: false,
        ));
        $logger->pushHandler($errorsHandler);

        $processor = new CallerAdderProcessor();
        $logger->pushProcessor($processor);

        return new LogRecordLogger($logger);
    }
}
