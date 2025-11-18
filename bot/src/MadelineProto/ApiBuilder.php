<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\MadelineProto;

use danog\MadelineProto\API;
use danog\MadelineProto\Exception;
use danog\MadelineProto\Logger as MadelineProtoLogger;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Settings\Logger;
use olml89\TelegramUserbot\Bot\Bot\BotConfig;
use olml89\TelegramUserbot\Bot\Bot\BotSession;
use olml89\TelegramUserbot\Bot\Bot\Status\StatusBroadcaster;
use olml89\TelegramUserbot\Bot\Output\ExceptionOutput;
use olml89\TelegramUserbot\Bot\Output\MadelineProtoCallableLoggerOutput;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\ErrorLogRecord;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use Stringable;

/**
 * It builds a MadelineProto MTProto API instance, attaching a callable logger into it to broadcast the current
 * process of the API (API::getAuthorization()) as new log events happen.
 */
final readonly class ApiBuilder
{
    public function __construct(
        private BotConfig $botConfig,
        private BotSession $botSession,
        private StatusBroadcaster $statusBroadcaster,
        private LoggableLogger $loggableLogger,
    ) {
    }

    public function build(): ?API
    {
        try {
            /**
             * @var ?API $api
             */
            $api = null;

            $appInfo = new AppInfo()
                ->setApiId($this->botConfig->apiId)
                ->setApiHash($this->botConfig->apiHash);

            $loggerSettings = new Logger()
                ->setType(MadelineProtoLogger::CALLABLE_LOGGER)
                ->setExtra(
                    // Pass $api by reference to the closure so it updates its value there once it is correctly instantiated
                    function (string|Stringable $output) use (&$api): void {
                        echo $output . PHP_EOL;
                        $this->statusBroadcaster->broadcast(
                            $api,
                            new MadelineProtoCallableLoggerOutput($output),
                        );
                    },
                );

            $settings = new Settings()
                ->setAppInfo($appInfo)
                ->setLogger($loggerSettings);

            $api = new API($this->botSession->path(), $settings);

            // Final broadcasting to get the current API status once the API instantiation has finished
            $this->statusBroadcaster->broadcast($api);

            return $api;
        } catch (Exception $e) {
            $this->statusBroadcaster->broadcast(output: new ExceptionOutput($e));
            $this->loggableLogger->log(new ErrorLogRecord('Error instantiating MadelineProto API', $e));

            return null;
        }
    }
}
