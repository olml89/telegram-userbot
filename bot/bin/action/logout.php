<?php

declare(strict_types=1);

use DI\Container;
use olml89\TelegramUserbot\Bot\Action\Action\Logout;
use olml89\TelegramUserbot\Bot\Action\ActionRunner;

/** @var Container $container */
$container = require dirname(__DIR__) . '/container.php';

/** @var Logout $action */
$action = $container->get(Logout::class);

/** @var ActionRunner $actionRunner */
$actionRunner = $container->get(ActionRunner::class);

$actionRunner->run($action);
