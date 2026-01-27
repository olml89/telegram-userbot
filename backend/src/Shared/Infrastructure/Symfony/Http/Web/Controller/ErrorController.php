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

        $errorTemplate = match($statusCode) {
            Response::HTTP_NOT_FOUND => 'error/404.html.twig',
            default => 'error/500.html.twig',
        };

        return $this->render($errorTemplate, [
            'active_menu' => null,
        ]);
    }
}
