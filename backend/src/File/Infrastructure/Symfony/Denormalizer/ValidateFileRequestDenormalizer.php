<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Denormalizer;

use olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Http\Api\Validate\ValidateFileRequest;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class ValidateFileRequestDenormalizer implements DenormalizerInterface
{
    /**
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        /**
         * It maps to false as the denormalization result cannot be cached because it depends on the data.
         */
        return [
            ValidateFileRequest::class => false,
        ];
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === ValidateFileRequest::class && is_array($data);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        /**
         * @var class-string<ValidateFileRequest> $type
         *
         * @var array{
         *     Type?: null|string,
         *     Event?: array{
         *         Upload?: array{
         *             MetaData?: array{
         *                 filename?: null|string,
         *                 filetype?: null|string,
         *             },
         *             Size?: null|int,
         *         },
         *     },
         * } $data
         */
        $eventType = $data['Type'] ?? null;
        $originalName = $data['Event']['Upload']['MetaData']['filename'] ?? null;
        $mimeType = $data['Event']['Upload']['MetaData']['filetype'] ?? null;
        $size = $data['Event']['Upload']['Size'] ?? null;

        return new ValidateFileRequest($eventType, $originalName, $mimeType, $size);
    }
}
