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
        echo sprintf('❌ [check-dockerfile-sync.php][%s] %s', $service, $e->getMessage()) . PHP_EOL;
        throw $e;
        //exit(1);
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

    /**
     * Extract extensions in dev/composer.json
     * - ext-*
     */
    $composerJson = json_decode(file_get_contents($composerJsonPath), true);
    $composerExtensions = [];

    foreach ($composerJson['require'] ?? [] as $package => $version) {
        if (str_starts_with($package, 'ext-')) {
            $composerExtensions[] = substr($package, 4);
        }
    }

    /**
     * Extract extensions from dev/Dockerfile
     * - docker-php-ext-install [extension]
     * - pecl install [extension]
     */
    $dockerFileLines = file($dockerfilePath, flags: FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $normalizedDockerfile = normalizeDockerfile($dockerFileLines);
    $ignorableDockerfileExtensions = ['xdebug'];
    $dockerfileExtensions = [];

    foreach ($normalizedDockerfile as $dockerfileLine) {
        $extensions = getLineExtensions($dockerfileLine);

        foreach ($extensions as $extension) {
            if (in_array($extension, $ignorableDockerfileExtensions, strict: true)) {
                continue;
            }

            if (in_array($extension, $dockerfileExtensions, strict: true)) {
                continue;
            }

            $dockerfileExtensions[] = $extension;
        }
    }

    /**
     * Compare extensions
     */
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

/**
 * @return list<string>
 */
function normalizeDockerfile(array $dockerFileLines): array
{
    $normalizedLines = [];
    $current = '';

    foreach ($dockerFileLines as $dockerFileLine) {
        $dockerFileLine = rtrim($dockerFileLine);

        if (strlen($dockerFileLine) === 0 || str_starts_with($dockerFileLine, '#')) {
            continue;
        }

        // continuation line: \ token
        if (str_ends_with($dockerFileLine, '\\')) {
            $current .= ' ' . rtrim($dockerFileLine, '\\');

            continue;
        }

        // normal line
        $current .= ' ' . $dockerFileLine;
        $normalizedLines[] = trim($current);
        $current = '';
    }

    return $normalizedLines;
}

/**
 * @return list<string>
 */
function getLineExtensions(string $line): array
{
    if (preg_match('/docker-php-ext-install\s+(.+?)(?:\s*&&|$)/s', $line, $m) === 1) {
        return formatExtensions($m[1]);
    }

    if (preg_match('/pecl\s+install\s+(.+?)(?:\s*&&|$)/s', $line, $m) === 1) {
        return formatExtensions($m[1]);
    }

    return [];
}

/**
 * @return list<string>
 */
function formatExtensions(string $lineWithExtensionMatch): array
{
    $items = explode(' ', $lineWithExtensionMatch);
    $extensions = [];

    foreach ($items as $item) {
        $extension = trim($item);

        if (strlen($extension) > 0) {
            $extensions[] = $extension;
        }
    }

    return $extensions;
}
