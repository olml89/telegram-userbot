<?php

declare(strict_types=1);

use DI\Container;
use olml89\TelegramUserbot\BotManager\Bot\Status\StatusInitializer;
use olml89\TelegramUserbot\BotManager\Websocket\WebSocketServer;
use olml89\TelegramUserbot\BotManager\Websocket\WebSocketServerConfig;

/** @var Container $container */
$container = require 'container.php';

/** @var StatusInitializer $statusInitializer */
$statusInitializer = $container->get(StatusInitializer::class);

/** @var WebSocketServerConfig $config */
$config = $container->get(WebSocketServerConfig::class);

/** @var WebSocketServer $webSocketServer */
$webSocketServer = $container->get(WebSocketServer::class);

$webSocketServer->listen($statusInitializer, $config);
