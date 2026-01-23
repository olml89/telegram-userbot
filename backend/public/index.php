<?php

declare(strict_types=1);

use olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Kernel;
use olml89\TelegramUserbot\Shared\App\Environment\Env;
use olml89\TelegramUserbot\Shared\App\Environment\Environment;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload_runtime.php';

// Load environment values
new Dotenv()->bootEnv(dirname(__DIR__) . '/.env');

// Return instantiated Kernel
$environment = Environment::load(Env::string('APP_ENV'));

return fn (array $context): Kernel => new Kernel($environment);
