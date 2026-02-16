<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\App\Environment;

use Dotenv\Dotenv;
use InvalidArgumentException;

/**
 * Wrapper to load environment variables from a .env file, and to get typed environment variables from the environment.
 */
final readonly class Env
{
    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException(sprintf('env file not found at %s', $path));
        }

        $dotEnv = DotEnv::createImmutable($path);
        $dotEnv->load();
    }

    public static function get(string $key, null|bool|int|float|string $default): null|bool|int|float|string
    {
        /** @var ?string $value */
        $value = $_ENV[$key] ?? null;

        if (is_null($value)) {
            return $default;
        }

        return match (true) {
            in_array($value, ['null', '(null)', 'NULL', '(NULL)'], strict: true) => null,
            default => $value,
        };
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    public static function string(string $key, ?string $default = null): string
    {
        $value = self::get($key, $default);

        if (is_null($value)) {
            throw new MissingEnvironmentVariableException($key);
        }

        return is_string($value) ? $value : (string) $value;
    }

    public static function nullableBool(string $key, ?bool $default = null): ?bool
    {
        $value = self::get($key, $default);

        if (is_null($value)) {
            return null;
        }

        return is_bool($value) ? $value : (bool) $value;
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    public static function bool(string $key, ?bool $default = null): bool
    {
        return self::nullableBool($key, $default) ?? throw new MissingEnvironmentVariableException($key);
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    public static function int(string $key, ?int $default = null): int
    {
        $value = self::get($key, $default);

        if (is_null($value)) {
            throw new MissingEnvironmentVariableException($key);
        }

        return is_int($value) ? $value : (int) $value;
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    public static function float(string $key, ?float $default = null): float
    {
        $value = self::get($key, $default);

        if (is_null($value)) {
            throw new MissingEnvironmentVariableException($key);
        }

        return is_float($value) ? $value : (float) $value;
    }
}
