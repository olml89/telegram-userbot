<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use olml89\TelegramUserbot\Backend\File\Domain\Size\Size;
use olml89\TelegramUserbot\Backend\File\Domain\Size\SizeException;

final class SizeType extends Type
{
    private const string NAME = 'fileSize';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getBigIntTypeDeclarationSQL($column);
    }

    /**
     * @throws InvalidType
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): int
    {
        if (is_int($value)) {
            return $value;
        }

        if ($value instanceof Size) {
            return $value->value;
        }

        throw InvalidType::new(
            value: $value,
            toType: self::NAME,
            possibleTypes: ['int', Size::class],
        );
    }

    /**
     * @throws InvalidType
     * @throws InvalidFormat
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): Size
    {
        if ($value instanceof Size) {
            return $value;
        }

        if (is_string($value) && is_numeric($value)) {
            $value = (int) $value;
        }

        if (!is_int($value)) {
            throw InvalidType::new(
                value: $value,
                toType: self::NAME,
                possibleTypes: ['int', Size::class],
            );
        }

        try {
            return new Size($value);
        } catch (SizeException $e) {
            throw InvalidFormat::new(
                value: (string) $value,
                toType: self::NAME,
                expectedFormat: $e->getMessage(),
            );
        }
    }
}
