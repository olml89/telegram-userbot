<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Http\Web\Request\Content;

use olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Http\Web\Request\FormRequest;
use olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Http\Web\Request\RequestData;

final readonly class UploadContentRequest extends FormRequest
{
    protected function initializeRequestData(): RequestData
    {
        return new UploadContentRequestData();
    }
}
