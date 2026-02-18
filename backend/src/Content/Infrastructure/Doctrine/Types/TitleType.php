<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Content\Infrastructure\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use olml89\TelegramUserbot\Backend\Content\Domain\Title\Title;
use olml89\TelegramUserbot\Backend\Content\Domain\Title\TitleLengthException;

final class TitleType extends Type
{
    private const string NAME = 'contentTitle';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL([
            'length' => Title::maxLength(),
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

        if ($value instanceof Title) {
            return $value->value;
        }

        throw InvalidType::new(
            value: $value,
            toType: self::NAME,
            possibleTypes: ['string', Title::class],
        );
    }

    /**
     * @throws InvalidType
     * @throws InvalidFormat
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): Title
    {
        if ($value instanceof Title) {
            return $value;
        }

        if (!is_string($value)) {
            throw InvalidType::new(
                value: $value,
                toType: self::NAME,
                possibleTypes: ['string', Title::class],
            );
        }

        try {
            return new Title($value);
        } catch (TitleLengthException $e) {
            throw InvalidFormat::new(
                value: $value,
                toType: self::NAME,
                expectedFormat: $e->getMessage(),
            );
        }
    }
}
