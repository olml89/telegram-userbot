<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Web\Controller;

use olml89\TelegramUserbot\Backend\Content\Application\UploadContentCommand;
use olml89\TelegramUserbot\Backend\Content\Application\UploadContentCommandHandler;
use olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Web\Request\UploadContentRequest;
use olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\UploadedFile\SymfonyUploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UploadContentController extends AbstractController
{
    public function __construct(
        private readonly UploadContentCommandHandler $uploadContentCommandHandler,
    ) {
    }

    #[Route('/content', name: 'content.upload', methods: ['POST'])]
    public function __invoke(UploadContentRequest $request): Response
    {
        if (!$request->isValid()) {
            foreach ($request->getErrors(deep: true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }

            return $this->render('content.html.twig', [
                'active_menu' => 'content.list',
                'form' => $request->createView(),
                'show_modal' => true,
            ]);
        }

        $uploadContentCommand = new UploadContentCommand(
            name: $request->requestData()->name,
            description: $request->requestData()->description,
            file: new SymfonyUploadedFile($request->requestData()->file),
            tags: $request->requestData()->tags,
        );

        !is_null($this->uploadContentCommandHandler->handle($uploadContentCommand))
            ? $this->addFlash('success', 'Content uploaded successfully')
            : $this->addFlash('error', 'Error uploading content');

        return $this->redirectToRoute('content.list');
    }
}
