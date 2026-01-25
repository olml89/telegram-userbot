<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Web\Controller;

use olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Web\Request\UploadContentRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UploadContentController extends AbstractController
{
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

        /**
         * @TODO: store the content
         */
        $this->addFlash('success', 'Content uploaded successfully');

        return $this->redirectToRoute('content.list');
    }
}
