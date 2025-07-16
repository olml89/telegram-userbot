<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;
use olml89\TelegramUserbot\Bot\Action\Action;
use olml89\TelegramUserbot\Bot\Action\ActionRunner;
use olml89\TelegramUserbot\Bot\Action\Logout;
use olml89\TelegramUserbot\Shared\Logger\Channel;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use olml89\TelegramUserbot\Shared\Logger\LogRecordLogger;
use olml89\TelegramUserbot\Shared\Logger\LogRecordLoggerFactory;

/** @var ContainerBuilder<Container> $containerBuilder */
$containerBuilder = require dirname(__DIR__).'/container-builder.php';

$containerBuilder->addDefinitions([

    LoggableLogger::class => DI\factory(function (Container $c): LogRecordLogger {
        /** @var LogRecordLoggerFactory $logRecordLoggerFactory */
        $logRecordLoggerFactory = $c->get(LogRecordLoggerFactory::class);

        return $logRecordLoggerFactory->create(Channel::Logout);
    }),

    Action::class => DI\autowire(Logout::class),

]);

/** @var ActionRunner $actionRunner */
$actionRunner = $containerBuilder->build()->get(ActionRunner::class);

$actionRunner->run();
