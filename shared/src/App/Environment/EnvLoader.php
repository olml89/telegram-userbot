<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\App\Environment;

use InvalidArgumentException;
use RuntimeException;

final readonly class EnvLoader
{
    /**
     * @throws MissingEnvironmentVariableException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function load(string $path): Environment
    {
        $environment = Environment::load();

        if ($environment === Environment::Production) {
            return $environment;
        }

        if (!file_exists($path)) {
            throw new InvalidArgumentException(sprintf('env file not found at %s', $path));
        }

        if (!class_exists(\Dotenv\Dotenv::class)) {
            throw new RuntimeException('vlucas/phpdotenv is required in non-production environments. Install it via composer require --dev vlucas/phpdotenv');
        }

        $dotEnv = \Dotenv\Dotenv::createImmutable($path);
        $dotEnv->load();

        return $environment;
    }
}
