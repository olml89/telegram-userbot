<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Bot\Status;

use JsonException;

final readonly class StatusFactory
{
    /**
     * @throws JsonException
     * @throws InvalidStatusTypeException
     */
    public function fromJson(string $json): Status
    {
        /** @var array<string, mixed> $data */
        $data = json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR);
        $typeValue = $data['type'] ?? null;
        $message = is_string($data['message'] ?? null) ? $data['message'] : null;
        $time = is_int($data['time'] ?? null) ? $data['time'] : null;

        if (is_null($typeValue)) {
            throw InvalidStatusTypeException::missingType();
        }

        if (!is_string($typeValue)) {
            throw InvalidStatusTypeException::notString();
        }

        if (is_null($statusType = StatusType::tryFrom($typeValue))) {
            throw InvalidStatusTypeException::invalidType($typeValue);
        }

        return new Status($statusType, $message, $time);
    }
}
