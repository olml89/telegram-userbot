<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception;

use InvalidArgumentException;
use Throwable;

final class ExceptionHandlerPipeline implements ExceptionHandler
{
    /**
     * @var ExceptionHandler[]
     */
    private array $exceptionHandlers = [];

    /**
     * @param array<int, mixed> $exceptionHandlers
     */
    public function __construct(array $exceptionHandlers)
    {
        foreach ($exceptionHandlers as $exceptionHandler) {
            if (!$exceptionHandler instanceof ExceptionHandler) {
                throw new InvalidArgumentException('Instance of %s expected');
            }

            $this->exceptionHandlers[] = $exceptionHandler;
        }
    }

    public function handle(Throwable $exception): void
    {
        foreach ($this->exceptionHandlers as $exceptionHandler) {
            $exceptionHandler->handle($exception);
        }
    }
}
