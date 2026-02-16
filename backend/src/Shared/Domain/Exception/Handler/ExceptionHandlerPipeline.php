<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Handler;

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
                throw new InvalidArgumentException(
                    sprintf('Instance of %s expected', ExceptionHandler::class),
                );
            }

            $this->exceptionHandlers[] = $exceptionHandler;
        }
    }

    public function handle(Throwable $exception, bool $handled = true): void
    {
        foreach ($this->exceptionHandlers as $exceptionHandler) {
            $exceptionHandler->handle($exception, $handled);
        }
    }
}
