<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Tag\Infrastructure\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name\Name;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name\NameLengthException;

final class NameType extends Type
{
    public const string NAME = 'tagName';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL([
            'length' => 50,
        ]);
    }

    /**
     * @throws InvalidType
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): string
    {
        if (!$value instanceof Name) {
            throw InvalidType::new(
                value: $value,
                toType: self::NAME,
                possibleTypes: [Name::class],
            );
        }

        return $value->value;
    }

    /**
     * @throws InvalidType
     * @throws InvalidFormat
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): Name
    {
        if (!is_string($value)) {
            throw InvalidType::new(
                value: $value,
                toType: self::NAME,
                possibleTypes: ['string'],
            );
        }

        try {
            return new Name($value);
        } catch (NameLengthException) {
            throw InvalidFormat::new(
                value: $value,
                toType: self::NAME,
                expectedFormat: 'string between 1 and 50 characters long',
            );
        }
    }
}
