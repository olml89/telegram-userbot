<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Http\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

final class ErrorController extends AbstractController
{
    public function __invoke(Request $request, Throwable $exception): Response
    {
        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        $statusMessage = match (true) {
            $statusCode >= 500 => 'Unexpected',
            default => Response::$statusTexts[$statusCode],
        };

        $errorMessage = match (true) {
            $statusCode === Response::HTTP_FORBIDDEN => 'You do not have permission to access this page.',
            $statusCode === Response::HTTP_NOT_FOUND => 'The page you are looking for could not be found.',
            $statusCode === Response::HTTP_METHOD_NOT_ALLOWED => 'This action is not allowed on this page',
            default => 'Something went wrong. Check out the application logs.',
        };

        return $this->render('error/error.html.twig', [
            'active_menu' => null,
            'status' => $statusMessage,
            'message' => $errorMessage,
        ]);
    }
}
