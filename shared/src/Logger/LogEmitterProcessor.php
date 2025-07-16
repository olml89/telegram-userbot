<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Logger;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

final readonly class LogEmitterProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $callerName = $this->getCaller();

        $serviceContext = is_null($callerName) ? [] : [
            'caller' => $callerName,
        ];

        return $record->with(
            context: array_merge(
                $record->context,
                $serviceContext,
            ),
        );
    }

    private function getCaller(): ?string
    {
        /**
         * @var array<int, array{file: string, line: int, function: string, class: string, type: string}> $trace
         */
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, limit: 7);

        /**
         * When StatusSubscriber calls $this->logRecordLogger->log(...) on line 40:
         *
         * 5 => [
         *      'file: '/telegram-userbot/bot-manager/src/Status/StatusSubscriber.php',
         *      'line': 40,
         *      'function': log,
         *      'class': 'olml89\TelegramUserbot\Shared\Logger\LogRecordLogger',
         *      'type' => '->',
         * ],
         * 6 => [
         *      'file: '/telegram-userbot/bot-manager/src/Status/StatusManager.php',
         *      'line': 35,
         *      'function': subscribe,
         *      'class': 'olml89\TelegramUserbot\BotManager\Status\StatusSubscriber',
         *      'type' => '->',
         * ],
        */
        $line = $trace[5]['line'] ?? null;
        $serviceClassName = $trace[6]['class'] ?? null;

        if (is_null($serviceClassName) || is_null($line)) {
            return null;
        }

        if (!class_exists($serviceClassName)) {
            return null;
        }

        return sprintf('%s:%s', $serviceClassName, $line);
    }
}
