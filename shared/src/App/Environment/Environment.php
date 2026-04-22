<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\App\Environment;

enum Environment: string
{
    case Development = 'dev';
    case Production = 'prod';
    case Testing = 'test';
    case CI = 'ci';

    public function label(): string
    {
        return match ($this) {
            self::Development => 'development',
            self::Production => 'production',
            self::Testing => 'testing',
            self::CI => 'ci',
        };
    }

    public function isDebuggable(): bool
    {
        return $this === self::Development;
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    public static function load(): self
    {
        $value = Env::string('APP_ENV');

        return self::from($value);
    }
}
