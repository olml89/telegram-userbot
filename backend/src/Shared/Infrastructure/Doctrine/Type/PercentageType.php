<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Percentage\Percentage;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Percentage\PercentageException;

final class PercentageType extends Type
{
    private const string NAME = 'percentage';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    /**
     * @throws InvalidType
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): int
    {
        if (is_int($value)) {
            return $value;
        }

        if ($value instanceof Percentage) {
            return $value->value;
        }

        throw InvalidType::new(
            value: $value,
            toType: self::NAME,
            possibleTypes: ['int', Percentage::class],
        );
    }

    /**
     * @throws InvalidType
     * @throws InvalidFormat
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): Percentage
    {
        if ($value instanceof Percentage) {
            return $value;
        }

        if (is_string($value) && is_numeric($value)) {
            $value = (int) $value;
        }

        if (!is_int($value)) {
            throw InvalidType::new(
                value: $value,
                toType: self::NAME,
                possibleTypes: ['int', Percentage::class],
            );
        }

        try {
            return new Percentage($value);
        } catch (PercentageException $e) {
            throw InvalidFormat::new(
                value: (string) $value,
                toType: self::NAME,
                expectedFormat: $e->getMessage(),
            );
        }
    }
}
