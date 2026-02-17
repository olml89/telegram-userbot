<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Http\Api\Validate;

use olml89\TelegramUserbot\Backend\File\Application\Validate\ValidateFileCommandHandler;
use olml89\TelegramUserbot\Backend\Shared\Application\Validation\ValidationException;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/files/validation',
    name: 'api.files.validate',
    defaults: ['_api' => true],
    methods: ['POST'],
)]
final readonly class ValidateFileController
{
    public function __construct(
        private ValidateFileCommandHandler $validateFileCommandHandler,
    ) {}

    /**
     * Allow returning no-200 responses from this endpoint, as tusd won't fail the validation hook if the response
     * is a 200.
     *
     * This makes us have to pre-consult this endpoint from the frontend to access the possible validation errors,
     * as tusd does not return the validation errors in the response body and wraps every non-200 response into a 500 one.
     *
     * @throws ValidationException
     */
    public function __invoke(#[MapRequestPayload] ValidateFileRequest $request): JsonResponse
    {
        $this->validateFileCommandHandler->handle($request->command());

        /**
         * Empty response, but it cannot be a 204 No Content as tusd gives the following error:
         * ERR_INTERNAL_SERVER_ERROR: failed to parse hook response: unexpected end of JSON input
         */
        return new JsonResponse(data: new stdClass(), status: Response::HTTP_OK);
    }
}
