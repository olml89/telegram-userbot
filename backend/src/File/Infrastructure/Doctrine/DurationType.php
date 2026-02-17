<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use olml89\TelegramUserbot\Backend\File\Domain\Duration\Duration;
use olml89\TelegramUserbot\Backend\File\Domain\Duration\DurationException;

final class DurationType extends Type
{
    private const string NAME = 'fileDuration';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDecimalTypeDeclarationSQL([
            'precision' => 10,
            'scale' => 2,
        ]);
    }

    /**
     * @throws InvalidType
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): float
    {
        if (is_float($value)) {
            return $value;
        }

        if ($value instanceof Duration) {
            return $value->value;
        }

        throw InvalidType::new(
            value: $value,
            toType: self::NAME,
            possibleTypes: ['float', Duration::class],
        );
    }

    /**
     * @throws InvalidType
     * @throws InvalidFormat
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): Duration
    {
        if ($value instanceof Duration) {
            return $value;
        }

        if (is_string($value) && is_numeric($value)) {
            $value = (float) $value;
        }

        if (!is_float($value)) {
            throw InvalidType::new(
                value: $value,
                toType: self::NAME,
                possibleTypes: ['float', Duration::class],
            );
        }

        try {
            return new Duration($value);
        } catch (DurationException $e) {
            throw InvalidFormat::new(
                value: (string) $value,
                toType: self::NAME,
                expectedFormat: $e->getMessage(),
            );
        }
    }
}
