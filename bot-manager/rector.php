<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\ParamTypeByMethodCallTypeRector;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->phpVersion(PhpVersion::PHP_85);

    $rectorConfig->sets([
        SetList::PHP_85,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::TYPE_DECLARATION,
    ]);

    $rectorConfig->paths([
        __DIR__ . '/bin',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    /**
     * We need to skip this rule for now, because the WebSocketServer has to implement the Ratchet\MessageInterface
     * contract, and we cannot type hint string $msg in the onMessage method.
     */
    $rectorConfig->skip([
        ParamTypeByMethodCallTypeRector::class,
        '/telegram-userbot/bot-manager/src/Websocket/WebSocketServer.php',
    ]);
};
