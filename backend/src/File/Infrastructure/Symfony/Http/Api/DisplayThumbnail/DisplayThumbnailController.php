<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Http\Api\DisplayThumbnail;

use olml89\TelegramUserbot\Backend\File\Application\DisplayThumbnail\DisplayThumbnailCommand;
use olml89\TelegramUserbot\Backend\File\Application\DisplayThumbnail\DisplayThumbnailCommandHandler;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotFoundException;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotReadableException;
use olml89\TelegramUserbot\Backend\File\Domain\Thumbnail\ThumbnailNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route(
    path: '/files/{publicId}/thumbnail',
    name: 'api.files.thumbnail.display',
    defaults: ['_api' => true],
    methods: ['GET'],
)]
final class DisplayThumbnailController extends AbstractController
{
    public function __construct(
        private readonly DisplayThumbnailCommandHandler $displayThumbnailCommandHandler,
    ) {}

    /**
     * @throws FileNotFoundException
     * @throws ThumbnailNotFoundException
     * @throws FileNotReadableException
     */
    public function __invoke(Uuid $publicId): BinaryFileResponse
    {
        $displayThumbnailCommand = new DisplayThumbnailCommand($publicId);
        $thumbnail = $this->displayThumbnailCommandHandler->handle($displayThumbnailCommand);

        return $this->file($thumbnail);
    }
}
