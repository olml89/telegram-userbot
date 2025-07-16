<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Logger;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Throwable;
use WeakMap;

final class LogRecordLoggerFactory
{
    private readonly LoggerConfig $config;

    /**
     * @var WeakMap<Channel, LogRecordLogger>
     */
    private WeakMap $cachedLoggers;

    public function __construct(LoggerConfig $config)
    {
        $this->config = $config;
        $this->cachedLoggers = new WeakMap();
    }

    public function create(Channel $channel): LogRecordLogger
    {
        if ($this->cachedLoggers->offsetExists($channel)) {
            return $this->cachedLoggers[$channel];
        }

        $logger = new Logger($channel->value);

        $logger->setExceptionHandler(function (Throwable $e): void {
            $logName = Channel::Monolog->logFilePath($this->config);
            $logDir = dirname($logName);

            if (!file_exists($logDir)) {
                mkdir($logDir, recursive: true);
            }

            file_put_contents(
                filename: $logName,
                data: json_encode([
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]),
                flags: FILE_APPEND,
            );
        });

        $handler = new StreamHandler(stream: $channel->logFilePath($this->config), level: $this->config->level);
        $handler->setFormatter(new JsonFormatter(ignoreEmptyContextAndExtra: true, includeStacktraces: true));
        $logger->pushHandler($handler);

        $processor = new LogEmitterProcessor();
        $logger->pushProcessor($processor);

        return $this->cachedLoggers[$channel] = new LogRecordLogger($logger);
    }
}
