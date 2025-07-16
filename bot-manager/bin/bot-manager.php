<?php

declare(strict_types=1);

use DI\Container;
use olml89\TelegramUserbot\BotManager\Websocket\WebSocketServer;

/** @var Container $container */
$container = require 'container.php';

/** @var WebSocketServer $webSocketServer */
$webSocketServer = $container->get(WebSocketServer::class);

$webSocketServer->listen();
