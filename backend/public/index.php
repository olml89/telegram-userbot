<?php

declare(strict_types=1);

use danog\MadelineProto\Settings\AppInfo;

require '../vendor/autoload.php';

$settings = new AppInfo()->setApiId(20437490)->setApiHash('e2edfdbfb461cdbfdeb1f059c0eb916e');

\olml89\TelegramUserbot\Backend\BotEventHandler::startAndLoop('bot.session', $settings);

/*
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

if ($requestUri === '/start') {
    //$controlManager->start();
    http_response_code(204);
} elseif ($requestUri === '/stop') {
    //$controlManager->stop();
    http_response_code(204);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found']);
}
*/
