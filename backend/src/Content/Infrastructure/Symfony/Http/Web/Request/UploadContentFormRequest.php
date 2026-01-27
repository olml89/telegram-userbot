<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Symfony\Http\Web\Request;

use olml89\TelegramUserbot\Backend\Content\Application\UploadContentCommand;
use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Http\Web\Request\FormRequest;
use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Http\Web\Request\FormData;

/**
 * @extends FormRequest<UploadContentCommand, UploadContentFormData>
 */
final readonly class UploadContentFormRequest extends FormRequest
{
    protected function initializeFormData(): FormData
    {
        return new UploadContentFormData();
    }
}
