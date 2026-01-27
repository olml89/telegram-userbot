<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Domain;

use InvalidArgumentException;
use Throwable;

final readonly class ExceptionHandlerPipeline implements ExceptionHandler
{
    /**
     * @var ExceptionHandler[]
     */
    private array $exceptionHandlers;

    public function __construct(array $exceptionHandlers)
    {
        foreach ($exceptionHandlers as $exceptionHandler) {
            if ($exceptionHandler instanceof ExceptionHandler) {
                continue;
            }

            throw new InvalidArgumentException(
                sprintf(
                    'Instance of %s expected, %s provided',
                    ExceptionHandler::class,
                    $exceptionHandler::class,
                ),
            );
        }

        $this->exceptionHandlers = $exceptionHandlers;
    }

    public function handle(Throwable $exception): void
    {
        foreach ($this->exceptionHandlers as $exceptionHandler) {
            $exceptionHandler->handle($exception);
        }
    }
}
