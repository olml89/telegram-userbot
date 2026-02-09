<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception;

use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationErrorBag;
use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

final readonly class ApiExceptionResponseMapper
{
    public function __construct(
        private bool $debug,
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
            $data['debug'] = $this->getExceptionChain($previous);
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
            $exception instanceof ValidationException => new UnprocessableEntityHttpException(
                message: $exception->getMessage(),
                previous: new ValidationFailedException(
                    value: null,
                    violations: new ConstraintViolationList(
                        array_map(
                            fn (ValidationErrorBag $errorBag, string $field): ConstraintViolation => new ConstraintViolation(
                                message: $errorBag->formatErrorMessages(),
                                messageTemplate: null,
                                parameters: [],
                                root: '',
                                propertyPath: $field,
                                invalidValue: null,
                            ),
                            $exception->errors(),
                            array_keys($exception->errors()),
                        ),
                    ),
                ),
            ),
            default => new HttpException(
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: 'Unexpected error',
                previous: $exception,
            ),
        };
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
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
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

    /**
     * @return array<int, array{class: string, message: string, trace: array<int, array<string, mixed>>}>
     */
    private function getExceptionChain(Throwable $exception): array
    {
        $chain = [];

        while (!is_null($exception)) {
            $chain[] = [
                'class' => $exception::class,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ];

            $exception = $exception->getPrevious();
        }

        return $chain;
    }
}
