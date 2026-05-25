#!/usr/bin/env php
<?php

declare(strict_types=1);

set_error_handler(function (int $severity, string $message, string $file, int $line): never {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

/**
 * It checks if the require section of the dev/composer.json is in sync with required php extensions from the services
 *
 * Usage: check-dev-php-extensions.php [-f]
 *
 * Options:
 *      -f    Force update the dev/composer.json with the missing php extensions from services
 */

$projectRoot = dirname(__DIR__, 6);

$modules = [
    'application',
    'bot-runtime',
    'bot',
    'bot-manager',
    'backend',
];

try {
    $forceUpdate = parseForceUpdateFlag($argv);

    $moduleExtensions = array_unique(
        array_reduce(
            $modules,
            fn (array $carryExtensions, string $module) => array_merge(
                $carryExtensions,
                getPhpExtensions($projectRoot, $module),
            ),
            initial: [],
        ),
    );

    $devExtensions = getPhpExtensions($projectRoot, 'dev');
    $missingExtensions = array_diff_key($moduleExtensions, $devExtensions);

    if (count($missingExtensions) === 0) {
        echo '🔍 [update-dev-php-extensions.php] up to date with the PHP extensions from services' . PHP_EOL;

        return;
    }

    if (!$forceUpdate) {
        throw new Exception(sprintf(
            'missing PHP extensions from services: %s',
            implode(', ', array_keys($missingExtensions)),
        ));
    }

    $requiredDevExtensions = array_merge($devExtensions, $missingExtensions);
    ksort($requiredDevExtensions);

    updateDevComposerJson($projectRoot, $requiredDevExtensions);

    echo sprintf(
        '📦 [update-dev-php-extensions.php] dev/composer.json updated with services required php extensions: %s' . PHP_EOL,
        implode(', ', array_keys($missingExtensions)),
    );
} catch (Exception $e) {
    echo sprintf('❌ [update-dev-php-extensions.php] %s)', $e->getMessage()) . PHP_EOL;
    exit(1);
}

/**
 * @throws Exception
 */
function parseForceUpdateFlag(array $argv): bool
{
    $forceUpdate = false;

    foreach (array_slice($argv, 1) as $arg) {
        match ($arg) {
            '-f' => $forceUpdate = true,
            default => throw new Exception(sprintf('unknown option: %s', $arg)),
        };
    }

    return $forceUpdate;
}

/**
 * Collect ext-* keys from a given module
 *
 * @throws Exception
 */
function getPhpExtensions(string $projectRoot, string $module): array
{
    $moduleComposerJsonPath = $projectRoot . '/' . $module . '/composer.json';
    $moduleExtensions = [];

    if (!file_exists($moduleComposerJsonPath)) {
        throw new Exception(sprintf('%s not found', $moduleComposerJsonPath));
    }

    $json = json_decode(file_get_contents($moduleComposerJsonPath), true);

    foreach ($json['require'] ?? [] as $package => $version) {
        if (str_starts_with($package, 'ext-')) {
            $moduleExtensions[$package] = $version;
        }
    }

    return $moduleExtensions;
}

function updateDevComposerJson(string $projectRoot, array $requiredDevExtensions): void
{
    $devComposerJsonPath = $projectRoot . '/dev/composer.json';
    $devComposerJson = json_decode(file_get_contents($devComposerJsonPath), true);
    $devComposerJson['require'] ??= [];
    $phpVersion = null;

    foreach (array_keys($devComposerJson['require']) as $key) {
        if ($key === 'php') {
            $phpVersion = $devComposerJson['require'][$key];
            unset($devComposerJson['require'][$key]);

            continue;
        }

        if (str_starts_with($key, 'ext-')) {
            unset($devComposerJson['require'][$key]);
        }
    }

    $devComposerJson['require']['php'] = $phpVersion ?? throw new RuntimeException(
        'No PHP requirement found in dev/composer.json',
    );

    $devComposerJson['require'] = array_merge(
        $devComposerJson['require'],
        $requiredDevExtensions,
    );

    file_put_contents(
        $devComposerJsonPath,
        json_encode($devComposerJson, flags: JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL,
    );
}
