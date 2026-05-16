<?php

declare(strict_types=1);

use DI\Container;
use olml89\TelegramUserbot\Bot\Action\Action\Loop;
use olml89\TelegramUserbot\Bot\Action\ActionRunner;

/** @var Container $container */
$container = require dirname(__DIR__) . '/container.php';

/** @var Loop $action */
$action = $container->get(Loop::class);

/** @var ActionRunner $actionRunner */
$actionRunner = $container->get(ActionRunner::class);

$actionRunner->run($action);
