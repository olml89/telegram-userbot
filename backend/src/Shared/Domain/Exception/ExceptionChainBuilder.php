<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception;

use Throwable;

final readonly class ExceptionChainBuilder
{
    /**
     * @return non-empty-list<array{
     *     class: class-string<Throwable>&literal-string,
     *     message: string,
     *     file: string,
     *     line: int,
     *     trace: array<int, mixed>,
     * }>
     */
    public function build(Throwable $exception, bool $includeTraceArgs = false): array
    {
        $chain = [];

        while (!is_null($exception)) {
            $chain[] = [
                'class' => $exception::class,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $this->getTrace($exception, $includeTraceArgs),
            ];

            $exception = $exception->getPrevious();
        }

        return $chain;
    }

    /**
     * @return array<int, mixed>
     */
    private function getTrace(Throwable $exception, bool $includeTraceArgs): array
    {
        return array_map(
            fn (array $traceItem): array => array_diff_key(
                $traceItem,
                $includeTraceArgs ? [] : array_flip(['args', 'type'])
            ),
            $exception->getTrace(),
        );
    }
}
