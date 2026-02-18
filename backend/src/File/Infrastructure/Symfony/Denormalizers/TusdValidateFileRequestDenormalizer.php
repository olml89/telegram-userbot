<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Denormalizers;

use olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Http\Api\Validate\TusdValidateFileRequest;
use olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Http\Api\Validate\ValidateFileRequest;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * This denormalizer is used to map the tusd pre-create event to the ValidateFileRequest.
 */
final readonly class TusdValidateFileRequestDenormalizer implements DenormalizerInterface
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
        if ($type !== ValidateFileRequest::class || !is_array($data)) {
            return false;
        }

        return array_key_exists('Type', $data)
            && array_key_exists('Event', $data)
            && is_array($data['Event'])
            && array_key_exists('Upload', $data['Event'])
            && is_array($data['Event']['Upload'])
            && array_key_exists('MetaData', $data['Event']['Upload'])
            && is_array($data['Event']['Upload']['MetaData'])
            && array_key_exists('filename', $data['Event']['Upload']['MetaData'])
            && array_key_exists('filetype', $data['Event']['Upload']['MetaData'])
            && array_key_exists('Size', $data['Event']['Upload']);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        /**
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

        return new TusdValidateFileRequest($eventType, $originalName, $mimeType, $size);
    }
}
