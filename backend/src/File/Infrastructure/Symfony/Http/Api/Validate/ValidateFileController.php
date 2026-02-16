<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Http\Api\Validate;

use olml89\TelegramUserbot\Backend\File\Application\Validate\ValidateFileCommandHandler;
use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Exception\ApiExceptionResponseMapper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

#[Route(
    path: '/files/validation',
    name: 'api.files.validate',
    defaults: ['_api' => true],
    methods: ['POST'],
)]
final readonly class ValidateFileController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private ValidateFileCommandHandler $validateFileCommandHandler,
        private ApiExceptionResponseMapper $apiExceptionResponseMapper,
    ) {}

    /**
     * This endpoint will be consumed by tusd, which interpretes any non-200 response as a 500 error. So we cannot
     * return validation errors as a 422 response here.
     *
     * We have to return all the responses as a 200, and check if it has a errors container in the content.
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validateFileRequest = $this->serializer->deserialize(
                $request->getContent(),
                type: ValidateFileRequest::class,
                format: 'json',
            );

            /**
             * This validates the raw contents of the request and is used to check if any data is missing.
             */
            $errors = $this->validator->validate($validateFileRequest);

            if (count($errors) > 0) {
                $validationFailedException = new ValidationFailedException(
                    value: null,
                    violations: $errors,
                );

                throw new UnprocessableEntityHttpException(
                    message: $validationFailedException->getMessage(),
                    previous: $validationFailedException,
                );
            }

            /**
             * This validates the domain logic which determines if a File is valid or not.
             */
            $this->validateFileCommandHandler->handle($validateFileRequest->command());

            return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
        } catch (Throwable $exception) {
            $apiErrorResponse = $this->apiExceptionResponseMapper->map($request, $exception);

            if (is_null($apiErrorResponse)) {
                return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
            }

            if ($apiErrorResponse->getStatusCode() !== Response::HTTP_UNPROCESSABLE_ENTITY) {
                return $apiErrorResponse;
            }

            $content = $apiErrorResponse->getContent();

            return new JsonResponse(
                data: $content === false ? null : json_decode($content, true),
                status: Response::HTTP_OK,
            );
        }
    }
}
