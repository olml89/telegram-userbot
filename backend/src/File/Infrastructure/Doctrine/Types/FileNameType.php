<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileName;
use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileNameLengthException;

final class FileNameType extends Type
{
    private const string NAME = 'fileName';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL([
            'length' => FileName::maxLength(),
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

        if ($value instanceof FileName) {
            return $value->value;
        }

        throw InvalidType::new(
            value: $value,
            toType: self::NAME,
            possibleTypes: ['string', FileName::class],
        );
    }

    /**
     * @throws InvalidType
     * @throws InvalidFormat
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): FileName
    {
        if ($value instanceof FileName) {
            return $value;
        }

        if (!is_string($value)) {
            throw InvalidType::new(
                value: $value,
                toType: self::NAME,
                possibleTypes: ['string', FileName::class],
            );
        }

        try {
            return new FileName($value);
        } catch (FileNameLengthException $e) {
            throw InvalidFormat::new(
                value: $value,
                toType: self::NAME,
                expectedFormat: $e->getMessage(),
            );
        }
    }
}
