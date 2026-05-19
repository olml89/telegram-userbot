#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Validates that the PHP extensions installed in dev/Dockerfile match those
 * declared in dev/composer.json. Exits with code 1 on mismatch.
 *
 * Usage: php validate-image-consistency.php
 */

$projectRoot = dirname(__DIR__, 3);
$dockerfilePath = $projectRoot . '/dev/Dockerfile';
$composerJsonPath = $projectRoot . '/dev/composer.json';

echo '🔍 Checking that dev/Dockerfile extensions match dev/composer.json...' . PHP_EOL;

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
    if (!preg_match('/^RUN\s+install-php-extensions\s+(.+)/', $dockerfileLine, $m)) {
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
sort($dockerfileExtensions);

echo 'dev/composer.json extensions: ' . implode(', ', $composerExtensions) . PHP_EOL;
echo 'dev/Dockerfile extensions: ' . implode(', ', $dockerfileExtensions) . PHP_EOL;

$inComposerOnly = array_diff($composerExtensions, $dockerfileExtensions);
$inDockerfileOnly = array_diff($dockerfileExtensions, $composerExtensions);

if ($inComposerOnly || $inDockerfileOnly) {
    echo PHP_EOL . '❌ Mismatch between dev/Dockerfile and dev/composer.json!' . PHP_EOL;

    if ($inComposerOnly) {
        echo PHP_EOL . 'In dev/composer.json but not in dev/Dockerfile:' . PHP_EOL;

        foreach ($inComposerOnly as $e) {
            echo '  - ' . $e . PHP_EOL;
        }
    }

    if ($inDockerfileOnly) {
        echo PHP_EOL . 'In dev/Dockerfile but not in dev/composer.json:' . PHP_EOL;

        foreach ($inDockerfileOnly as $e) {
            echo '  - ' . $e . PHP_EOL;
        }
    }

    exit(1);
}

echo PHP_EOL . '✅ dev/Dockerfile and dev/composer.json extensions are in sync.' . PHP_EOL;
