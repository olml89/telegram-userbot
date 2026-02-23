<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileName;

use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\StringValueObject;
use Symfony\Component\Uid\Uuid;

final readonly class FileName extends StringValueObject
{
    private const int MIN_LENGTH = 1;
    private const int MAX_LENGTH = 40;

    public static function from(Uuid $name, string $extension): self
    {
        return new self($name->toRfc4122() . '.' . $extension);
    }

    public static function maxLength(): int
    {
        return self::MAX_LENGTH;
    }

    /**
     * @throws FileNameLengthException
     */
    protected static function validate(string $value): void
    {
        if (mb_strlen($value) < self::MIN_LENGTH || mb_strlen($value) > self::MAX_LENGTH) {
            throw new FileNameLengthException(self::MIN_LENGTH, self::MAX_LENGTH);
        }
    }

    private function sharding(): string
    {
        return implode(
            separator: '/',
            array: array_slice(
                array: explode('-', $this->value),
                offset: 0,
                length: 2,
            ),
        );
    }

    public function directoryPath(string $contentDirectory): string
    {
        return sprintf(
            '%s/%s',
            $contentDirectory,
            $this->sharding(),
        );
    }

    public function filePath(string $contentDirectory): string
    {
        return sprintf(
            '%s/%s',
            $this->directoryPath($contentDirectory),
            $this->value,
        );
    }
}
