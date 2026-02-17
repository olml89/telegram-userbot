<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use olml89\TelegramUserbot\Backend\Content\Domain\Description\Description;
use olml89\TelegramUserbot\Backend\Content\Domain\Description\DescriptionLengthException;

final class DescriptionType extends Type
{
    private const string NAME = 'contentDescription';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getClobTypeDeclarationSQL($column);
    }

    /**
     * @throws InvalidType
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): string
    {
        if (is_string($value)) {
            return $value;
        }

        if ($value instanceof Description) {
            return $value->value;
        }

        throw InvalidType::new(
            value: $value,
            toType: self::NAME,
            possibleTypes: ['string', Description::class],
        );
    }

    /**
     * @throws InvalidType
     * @throws InvalidFormat
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): Description
    {
        if ($value instanceof Description) {
            return $value;
        }

        if (!is_string($value)) {
            throw InvalidType::new(
                value: $value,
                toType: self::NAME,
                possibleTypes: ['string', Description::class],
            );
        }

        try {
            return new Description($value);
        } catch (DescriptionLengthException $e) {
            throw InvalidFormat::new(
                value: $value,
                toType: self::NAME,
                expectedFormat: $e->getMessage(),
            );
        }
    }
}
