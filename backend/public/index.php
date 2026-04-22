<?php

declare(strict_types=1);

use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Kernel;
use olml89\TelegramUserbot\Shared\App\Environment\Env;
use olml89\TelegramUserbot\Shared\App\Environment\Environment;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Load backend autoloader
 * (autoload_runtime allows to only return the Kernel, letting Symfony deal with the request/response cycle)
 */
require dirname(__DIR__) . '/vendor/autoload_runtime.php';

/**
 * Load shared and backend env vars
 */
$environment = Environment::load();

if ($environment !== Environment::Production) {
    new Dotenv()->bootEnv(dirname(__DIR__, 2) . '/shared/.env');
    new Dotenv()->bootEnv(dirname(__DIR__) . '/.env', overrideExistingVars: true);
}

/**
 * Return instantiated Kernel
 */
$debug = Env::nullableBool('APP_DEBUG');

return fn(array $context): Kernel => new Kernel($environment, $debug);
