<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\StringLengthException;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name\Name;

final class NameType extends Type
{
    private const string NAME = 'name';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL([
            'length' => Name::maxLength(),
        ]);
    }

    /**
     * @throws InvalidType
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): string
    {
        if (is_string($value)) {
            return $value;
        }

        if ($value instanceof Name) {
            return $value->value;
        }

        throw InvalidType::new(
            value: $value,
            toType: self::NAME,
            possibleTypes: ['string', Name::class],
        );
    }

    /**
     * @throws InvalidType
     * @throws InvalidFormat
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): Name
    {
        if ($value instanceof Name) {
            return $value;
        }

        if (!is_string($value)) {
            throw InvalidType::new(
                value: $value,
                toType: self::NAME,
                possibleTypes: ['string', Name::class],
            );
        }

        try {
            return new Name($value);
        } catch (StringLengthException $e) {
            throw InvalidFormat::new(
                value: $value,
                toType: self::NAME,
                expectedFormat: $e->getMessage(),
            );
        }
    }
}
