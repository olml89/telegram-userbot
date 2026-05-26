#!/usr/bin/env php
<?php

declare(strict_types=1);

set_error_handler(function (int $severity, string $message, string $file, int $line): never {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

/**
 * It checks if the PHP extensions of a service are in sync:
 *     composer.json    <->    Dockerfile
 *
 * Usage: check-dockerfile-sync.php [SERVICES...]
 *
 * Arguments:
 *      [SERVICES...]    The services to analyse (application, bot-runtime, bot, bot-manager, backend, dev)
 */

$projectRoot = dirname(__DIR__, 6);

try {
    $services = parseServices($argv);
} catch (Exception $e) {
    echo sprintf('❌ [check-dockerfile-sync.php] %s)', $e->getMessage()) . PHP_EOL;
    exit(1);
}

foreach ($services as $service) {
    $composerJsonPath = sprintf('%s/%s/composer.json', $projectRoot, $service);
    $dockerfilePath = sprintf('%s/%s/Dockerfile', $projectRoot, $service);

    try {
        assertDockerfileExtensionsMatchComposerExtensions($composerJsonPath, $dockerfilePath);

        echo sprintf(
            '🔍 [check-dockerfile-sync.php][%s] %s extensions and %s extensions are in sync.' . PHP_EOL,
            $service,
            $composerJsonPath,
            $dockerfilePath,
        );
    } catch (Exception $e) {
        echo sprintf('❌ [check-dockerfile-sync.php][%s] %s)', $service, $e->getMessage()) . PHP_EOL;
        exit(1);
    }
}

/**
 * @throws Exception
 */
function parseServices(array $argv): array
{
    $services = [];

    foreach (array_slice($argv, 1) as $arg) {
        match ($arg) {
            'bot', 'bot-manager', 'backend', 'dev' => $services[] = $arg,
            default => throw new Exception(sprintf('service without Dockerfile: %s', $arg)),
        };
    }

    return $services;
}

/**
 * @throws Exception
 */
function assertDockerfileExtensionsMatchComposerExtensions(string $composerJsonPath, string $dockerfilePath): void
{
    if (!file_exists($composerJsonPath)) {
        throw new Exception(sprintf('%s not found', $composerJsonPath));
    }

    if (!file_exists($dockerfilePath)) {
        throw new Exception(sprintf('%s not found', $dockerfilePath));
    }

    // Extract ext-* from dev/composer.json
    $composerJson = json_decode(file_get_contents($composerJsonPath), true);
    $composerExtensions = [];

    foreach ($composerJson['require'] ?? [] as $package => $version) {
        if (str_starts_with($package, 'ext-')) {
            $composerExtensions[] = substr($package, 4);
        }
    }

    sort($composerExtensions);

    // Extract extensions from RUN install-php-extensions lines in dev/Dockerfile
    $dockerFile = file($dockerfilePath, flags: FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $ignorableExtensions = ['xdebug', '@composer'];
    $dockerfileExtensions = [];

    foreach ($dockerFile as $dockerfileLine) {
        $dockerfileLine = trim($dockerfileLine);

        // Only match lines that are RUN install-php-extensions commands
        // Cut the line before any &&
        if (!preg_match('/^RUN\s+install-php-extensions\s+(.+?)(?:\s*&&|$)/', $dockerfileLine, $m)) {
            continue;
        }

        // Strip inline comments
        $extensions = preg_replace('/#.*/', '', $m[1]);

        foreach (preg_split('/\s+/', trim($extensions)) as $extension) {
            if ($extension !== '' && !in_array($extension, $ignorableExtensions, true)) {
                $dockerfileExtensions[] = $extension;
            }
        }
    }

    $dockerfileExtensions = array_unique($dockerfileExtensions);
    $inComposerOnly = array_diff($composerExtensions, $dockerfileExtensions);
    $inDockerfileOnly = array_diff($dockerfileExtensions, $composerExtensions);

    if (count($inComposerOnly) > 0) {
        throw new Exception(sprintf(
            '%s is missing extensions in %s: (%s)' . PHP_EOL,
            $dockerfilePath,
            $composerJsonPath,
            implode(', ', $inComposerOnly),
        ));
    }

    if (count($inDockerfileOnly) > 0) {
        throw new Exception(sprintf(
            '%s is missing extensions in %s: (%s)' . PHP_EOL,
            $composerJsonPath,
            $dockerfilePath,
            implode(', ', $inDockerfileOnly),
        ));
    }
}
