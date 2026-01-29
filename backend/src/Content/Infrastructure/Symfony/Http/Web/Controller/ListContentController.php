<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Web\Controller;

use olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Form\ContentType;
use olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Web\Request\UploadContentFormData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListContentController extends AbstractController
{
    #[Route('/library', name: 'library.list', methods: ['GET'])]
    public function __invoke(): Response
    {
        $uploadContentFormData = new UploadContentFormData();
        $form = $this->createForm(ContentType::class, $uploadContentFormData);

        return $this->render('pages/library/list.html.twig', [
            'active_menu' => 'library.list',
            'form' => $form->createView(),
            'show_modal' => false,
        ]);
    }
}
