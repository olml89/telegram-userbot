<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception;

use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\ExceptionAggregator;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\ExceptionChainBuilder;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\InvalidResourceException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

final readonly class ApiExceptionResponseMapper
{
    public function __construct(
        private bool $debug,
        private ExceptionChainBuilder $exceptionChainBuilder,
    ) {
    }

    public function map(Request $request, Throwable $exception): ?JsonResponse
    {
        if ($request->attributes->get('_api') !== true) {
            return null;
        }

        $exception = $this->convertToHttpException($exception);
        $data = $this->getResponseData($exception);

        if ($this->debug && !is_null($previous = $exception->getPrevious())) {
            $data['debug'] = $this->exceptionChainBuilder->build($previous);

            if ($previous instanceof ExceptionAggregator && count($previous->getAggregatedExceptions()) > 0) {
                $data['aggregatedExceptions'] = [];

                foreach ($previous->getAggregatedExceptions() as $aggregatedException) {
                    $data['aggregatedExceptions'][] = $this->exceptionChainBuilder->build($aggregatedException);
                }
            }
        }

        return new JsonResponse(
            data: $data,
            status: $exception->getStatusCode(),
            headers: $exception->getHeaders(),
        );
    }

    private function convertToHttpException(Throwable $exception): HttpExceptionInterface
    {
        return match (true) {
            $exception instanceof HttpExceptionInterface => $exception,
            $exception instanceof NotFoundException => new NotFoundHttpException(
                message: $exception->getMessage(),
                previous: $exception,
            ),
            $exception instanceof InvalidResourceException => new UnsupportedMediaTypeHttpException(
                message: $exception->getMessage(),
                previous: $exception,
            ),
            $exception instanceof ValidationException => new UnprocessableEntityHttpException(
                message: $exception->getMessage(),
                previous: new ValidationFailedException(
                    value: null,
                    violations: $this->buildConstraintViolationList($exception),
                ),
            ),
            default => new HttpException(
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: 'Unexpected error',
                previous: $exception,
            ),
        };
    }

    private function buildConstraintViolationList(ValidationException $exception): ConstraintViolationList
    {
        $constraintViolationList = new ConstraintViolationList();

        foreach ($exception->errors() as $field => $validationErrorBag) {
            foreach ($validationErrorBag as $errorMessage) {
                $constraintViolationList->add(
                    new ConstraintViolation(
                        message: $errorMessage,
                        messageTemplate: null,
                        parameters: [],
                        root: '',
                        propertyPath: $field,
                        invalidValue: null,
                    ),
                );
            }
        }

        return $constraintViolationList;
    }

    /**
     * @return array<string, mixed>
     */
    private function getResponseData(HttpExceptionInterface $exception): array
    {
        if (!$exception instanceof UnprocessableEntityHttpException) {
            return $this->getStandardResponseData($exception);
        }

        $validationException = $exception->getPrevious();

        if (!$validationException instanceof ValidationFailedException) {
            return $this->getStandardResponseData($exception);
        }

        $errors = [];

        foreach ($validationException->getViolations() as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return [
            'message' => 'Validation failed.',
            'errors' => $errors,
        ];
    }

    /**
     * @return array{message: string}
     */
    private function getStandardResponseData(HttpExceptionInterface $exception): array
    {
        return [
            'message' => $exception->getMessage(),
        ];
    }
}
