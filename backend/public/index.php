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
 * Load shared env vars
 */
new Dotenv()->bootEnv(dirname(__DIR__, 2) . '/shared/.env');

/**
 * Load backend env vars
 */
new Dotenv()->bootEnv(dirname(__DIR__) . '/.env');

/**
 * Return instantiated Kernel
 */
$environment = Environment::load(Env::string('APP_ENV'));

return fn (array $context): Kernel => new Kernel($environment);
