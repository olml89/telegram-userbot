<?php

declare(strict_types=1);

use olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Kernel;
use olml89\TelegramUserbot\Shared\App\Environment\Environment;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__) . '/vendor/autoload_runtime.php';

// Load environment values
new Dotenv()->bootEnv(dirname(__DIR__) . '/.env');

// Load Kernel and Request
$environment = Environment::load($_SERVER['APP_ENV']);
$kernel = new Kernel($environment->value, $environment->isDebuggable());
$request = Request::createFromGlobals();

// Process response
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
