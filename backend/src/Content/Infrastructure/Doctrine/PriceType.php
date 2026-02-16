<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use olml89\TelegramUserbot\Backend\Content\Domain\Price;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\OutOfRangeException;

final class PriceType extends Type
{
    private const string NAME = 'contentPrice';

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

        if ($value instanceof Price) {
            return $value->value;
        }

        throw InvalidType::new(
            value: $value,
            toType: self::NAME,
            possibleTypes: ['float', Price::class],
        );
    }

    /**
     * @throws InvalidType
     * @throws InvalidFormat
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): Price
    {
        if ($value instanceof Price) {
            return $value;
        }

        if (is_string($value) && is_numeric($value)) {
            $value = (float) $value;
        }

        if (!is_float($value)) {
            throw InvalidType::new(
                value: $value,
                toType: self::NAME,
                possibleTypes: ['float', Price::class],
            );
        }

        try {
            return new Price($value);
        } catch (OutOfRangeException $e) {
            throw InvalidFormat::new(
                value: (string) $value,
                toType: self::NAME,
                expectedFormat: $e->getMessage(),
            );
        }
    }
}
