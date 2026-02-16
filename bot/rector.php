<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->phpVersion(PhpVersion::PHP_85);

    $rectorConfig->sets([
        SetList::PHP_85,
    ]);

    $rectorConfig->paths([
        __DIR__ . '/bin',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);
};
