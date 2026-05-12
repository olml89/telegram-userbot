<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Http\Api\Display;

use olml89\TelegramUserbot\Backend\File\Application\Display\DisplayCommand;
use olml89\TelegramUserbot\Backend\File\Application\Display\DisplayCommandHandler;
use olml89\TelegramUserbot\Backend\File\Domain\FileNotFoundException;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFileNotReadableException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route(
    path: '/files/{publicId}',
    name: 'api.files.display',
    defaults: ['_api' => true],
    methods: ['GET'],
)]
final class DisplayFileController extends AbstractController
{
    public function __construct(
        private readonly DisplayCommandHandler $displayCommandHandler,
    ) {}

    /**
     * @throws FileNotFoundException
     * @throws StorageFileNotReadableException
     */
    public function __invoke(Uuid $publicId): BinaryFileResponse
    {
        $displayCommand = new DisplayCommand($publicId);
        $file = $this->displayCommandHandler->handle($displayCommand);

        /**
         * Content-Disposition: inline; the browser opens a file (inline) instead of downloading it.
         */
        return $this->file($file, disposition: ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
