<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotRuntime\Logger;

use Throwable;

final readonly class ExceptionEncoder
{
    public static function encode(Throwable $e): string
    {
        try {
            $serializedTrace = array_map(
                static fn(array $frame): array => array_diff_key(
                    $frame,
                    array_flip([
                        'object',
                        'args',
                    ]),
                ),
                $e->getTrace(),
            );

            return json_encode(
                value: [
                    'exception' => $e::class,
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $serializedTrace,
                ],
                flags: JSON_THROW_ON_ERROR,
            );
        } catch (Throwable $encodingException) {
            return sprintf(
                'Error serializing exception: %s (Exception: %s)',
                $encodingException->getMessage(),
                $e->getMessage(),
            );
        }
    }
}
