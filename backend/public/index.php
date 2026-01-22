<?php

declare(strict_types=1);

use olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Kernel;
use olml89\TelegramUserbot\Shared\App\Environment\Environment;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__) . '/vendor/autoload_runtime.php';

$environment = Environment::load($_SERVER['APP_ENV']);
$kernel = new Kernel($environment->value, $environment->isDebuggable());
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
