<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\FileName;

use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\StringValueObject;
use Symfony\Component\Uid\Uuid;

final readonly class FileName extends StringValueObject
{
    private const int MIN_LENGTH = 1;
    private const int MAX_LENGTH = 45;

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
        $shardBase = str_replace('-', '', $this->value);

        /**
         * Improved sharding: 2-level sharding path using UUID hex characters.
         *
         * Distributes files across 65,536 (256×256) possible combinations by extracting consecutive hex pairs.
         * This approach:
         * - Provides uniform distribution across filesystem
         * - Prevents directory clustering
         * - Scales efficiently without reorganization
         *
         * Example: UUID 550e8400-e29b-41d4-a716-446655440000
         *          Result: /55/0e/550e8400e29b41d4a716446655440000
         */
        return implode(
            separator: '/',
            array: [
                substr($shardBase, offset: 0, length: 2),
                substr($shardBase, offset: 2, length: 2),
            ],
        );
    }

    public function filePath(string $baseDirectory): string
    {
        return sprintf(
            '%s/%s/%s',
            $baseDirectory,
            $this->sharding(),
            $this->value,
        );
    }
}
