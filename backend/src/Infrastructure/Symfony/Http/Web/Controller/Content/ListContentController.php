<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Http\Web\Controller\Content;

use olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Http\Web\Form\ContentType;
use olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Http\Web\Request\Content\UploadContentRequestData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListContentController extends AbstractController
{
    #[Route('/content', name: 'content.list', methods: ['GET'])]
    public function __invoke(): Response
    {
        $uploadContentRequest = new UploadContentRequestData();
        $form = $this->createForm(ContentType::class, $uploadContentRequest);

        return $this->render('content.html.twig', [
            'active_menu' => 'content.list',
            'form' => $form->createView(),
            'show_modal' => false,
        ]);
    }
}
