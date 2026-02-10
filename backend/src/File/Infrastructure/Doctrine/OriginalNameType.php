<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use olml89\TelegramUserbot\Backend\File\Domain\OriginalName;
use olml89\TelegramUserbot\Backend\Shared\Domain\Exception\Invariant\StringLengthException;

final class OriginalNameType extends Type
{
    private const string NAME = 'fileOriginalName';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL([
            'length' => OriginalName::maxLength(),
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

        if ($value instanceof OriginalName) {
            return $value->value;
        }

        throw InvalidType::new(
            value: $value,
            toType: self::NAME,
            possibleTypes: ['string', OriginalName::class],
        );
    }

    /**
     * @throws InvalidType
     * @throws InvalidFormat
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): OriginalName
    {
        if ($value instanceof OriginalName) {
            return $value;
        }

        if (!is_string($value)) {
            throw InvalidType::new(
                value: $value,
                toType: self::NAME,
                possibleTypes: ['string', OriginalName::class],
            );
        }

        try {
            return new OriginalName($value);
        } catch (StringLengthException $e) {
            throw InvalidFormat::new(
                value: $value,
                toType: self::NAME,
                expectedFormat: $e->getMessage(),
            );
        }
    }
}
